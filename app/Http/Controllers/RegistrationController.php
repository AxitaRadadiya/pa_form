<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Relation;
use App\Models\Food;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Member;
use App\Models\Award;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ---------------------------------------------------------------
    // Helper: check if current user is admin
    // ---------------------------------------------------------------
    private function isAdmin(): bool
    {
        $user = auth()->user();
        return $user && (
            (isset($user->role) && $user->role === 'admin') ||
            (isset($user->email) && $user->email === 'superadmin@gmail.com')
        );
    }

    // ---------------------------------------------------------------
    // INDEX
    // ---------------------------------------------------------------
    public function index()
    {
        if ($this->isAdmin()) {
            $registrations = Registration::with('event', 'chapter', 'user')
                ->orderByDesc('created_at')
                ->paginate(25);
        } else {
            $registrations = Registration::where('user_id', auth()->id())
                ->with('event')
                ->orderByDesc('created_at')
                ->paginate(25);
        }

        return view('admin.registrations.index', compact('registrations'));
    }

    // ---------------------------------------------------------------
    // SHOW
    // ---------------------------------------------------------------
    public function show($id)
    {
        $reg = Registration::with(['members.relation', 'members.food', 'award.food', 'award.relation'])
            ->findOrFail($id);

        if (!$this->isAdmin() && $reg->user_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.registrations.show', compact('reg'));
    }

    // ---------------------------------------------------------------
    // CREATE FORM
    // ---------------------------------------------------------------
    public function create(Request $request)
    {
        $chapters  = Chapter::orderBy('name')->get();
        $relations = Relation::orderBy('name')->get();
        $foods     = Food::orderBy('name')->get();

        $event = null;
        if ($request->has('event')) {
            $event = Event::find($request->get('event'));
        }

        return view('admin.eventsForm.create', compact('chapters', 'relations', 'foods', 'event'));
    }

    // ---------------------------------------------------------------
    // STORE
    // ---------------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'chapter_id'             => 'required|integer|exists:chapters,id',
            'chain_name'             => 'required|string|max:255',
            'company_name'           => 'nullable|string|max:255',
            'about_company'          => 'nullable|string',
            'event_id'               => 'nullable|integer|exists:events,id',
            'transaction_id'         => 'nullable|string|max:255',
            'grand_total'            => 'nullable|numeric',

            // Members (Section 2) - array fields
            'member_name'            => 'nullable|array',
            'member_name.*'          => 'nullable|string|max:255',
            'member_mobile'          => 'nullable|array',
            'member_mobile.*'        => 'nullable|string|max:30',
            'relation_id'            => 'nullable|array',
            'relation_id.*'          => 'nullable|integer|exists:relations,id',
            'dob'                    => 'nullable|array',
            'dob.*'                  => 'nullable|date',
            'age'                    => 'nullable|array',
            'age.*'                  => 'nullable|integer',
            'food_id'                => 'nullable|array',
            'food_id.*'              => 'nullable|integer|exists:foods,id',
            'amount'                 => 'nullable|array',
            'amount.*'               => 'nullable|numeric',
            'section_description'    => 'nullable|string',

            // Award (Section 3)
            'section3_description'   => 'nullable|string',
            'member_first_name'      => 'nullable|string|max:255',
            'member_last_name'       => 'nullable|string|max:255',
            'award_name'             => 'nullable|string|max:255',
            'gender'                 => 'nullable|string|max:255',
            'award_type'             => 'nullable|string|in:certificate,award',
            'photo_attached'         => 'nullable|image|max:2048',
            'food_id_section3'       => 'nullable|integer|exists:foods,id',
            'relation_id_section3'   => 'nullable|integer|exists:relations,id',
            'amount_section3'        => 'nullable|numeric',
            'special_comment'        => 'nullable|string',

            // Files
            'company_logo'           => 'nullable|image|max:2048',
            'qr_code'                => 'nullable|image|max:2048',
            'screenshot_payment'     => 'nullable|image|max:4096',
        ]);
       
        // --- Upload files ---
        $files = $this->uploadFiles($request, [
            'company_logo', 'qr_code', 'screenshot_payment', 'photo_attached'
        ]);

        // --- Build members list ---
        $members = $this->buildMembers($request);

        // --- Recalculate award amount server-side ---
        $amountSection3 = $this->calcAwardAmount($request);

        // --- Recalculate grand total server-side ---
        $grandTotal = array_sum(array_column($members, 'amount')) + $amountSection3;

        // Helpful debug: log key inputs (exclude file uploads) and computed values
        try {
            Log::debug('Registration store input', [
                'user_id' => auth()->id(),
                'request' => $request->except(['company_logo','qr_code','screenshot_payment','photo_attached']),
            ]);
            Log::debug('Computed members and totals for registration store', [
                'members_count' => count($members),
                'members' => $members,
                'amount_section3' => $amountSection3,
                'grand_total' => $grandTotal,
            ]);
        } catch (\Exception $e) {
            // swallow logging errors but record them
            Log::warning('Failed to write debug log for registration store: '.$e->getMessage());
        }

        DB::beginTransaction();
        try {
            // 1. Create Registration (Section 1)
            $reg = Registration::create([
                'user_id'             => auth()->id(),
                'event_id'            => $request->input('event_id'),
                'chapter_id'          => $request->input('chapter_id'),
                'chain_name'          => $request->input('chain_name'),
                'company_name'        => $request->input('company_name'),
                'about_company'       => $request->input('about_company'),
                'company_logo'        => $files['company_logo'] ?? null,
                'qr_code'             => $files['qr_code'] ?? null,
                'screenshot_payment'  => $files['screenshot_payment'] ?? null,
                'grand_total'         => $grandTotal,
                'transaction_id'      => $request->input('transaction_id'),
            ]);

            // 2. Create Members (Section 2)
            foreach ($members as $memberData) {
                Member::create(array_merge($memberData, ['registration_id' => $reg->id]));
            }

            // 3. Create Award (Section 3) — only if any award field is filled
            if ($this->hasAwardData($request)) {
                Award::create([
                    'registration_id'      => $reg->id,
                    'section3_description' => $request->input('section3_description'),
                    'first_name'           => $request->input('member_first_name'),
                    'last_name'            => $request->input('member_last_name'),
                    'award_name'           => $request->input('award_name'),
                    'gender'               => $request->input('gender'),
                    'award_type'           => $request->input('award_type'),
                    'photo_attached'       => $files['photo_attached'] ?? null,
                    'food_id'              => $request->input('food_id_section3'),
                    'relation_id'          => $request->input('relation_id_section3'),
                    'amount_section3'      => $amountSection3,
                    'special_comment'      => $request->input('special_comment'),
                ]);
            }
            
            DB::commit();
            Log::info('Registration stored', ['id' => $reg->id, 'user' => auth()->id()]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store registration', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $msg = config('app.debug') ? 'Failed to save registration: ' . $e->getMessage() : 'Failed to save registration.';
            return back()->withInput()->with('error', $msg);
        }
        

        return $this->isAdmin()
            ? redirect()->route('dashboard')->withSuccess('Registration submitted successfully.')
            : redirect()->route('user.dashboard')->withSuccess('Registration submitted successfully.');
    }

    // ---------------------------------------------------------------
    // EDIT FORM
    // ---------------------------------------------------------------
    public function edit($id)
    {
        $reg = Registration::with(['members', 'award'])->findOrFail($id);

        if (!$this->isAdmin() && $reg->user_id !== auth()->id()) {
            abort(403);
        }

        $chapters  = Chapter::orderBy('name')->get();
        $relations = Relation::orderBy('name')->get();
        $foods     = Food::orderBy('name')->get();

        return view('admin.registrations.edit', compact('reg', 'chapters', 'relations', 'foods'));
    }

    // ---------------------------------------------------------------
    // UPDATE
    // ---------------------------------------------------------------
    public function update(Request $request, $id)
    {
        $reg = Registration::with(['members', 'award'])->findOrFail($id);

        if (!$this->isAdmin() && $reg->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'chapter_id'             => 'required|integer|exists:chapters,id',
            'chain_name'             => 'required|string|max:255',
            'company_name'           => 'nullable|string|max:255',
            'about_company'          => 'nullable|string',
            'event_id'               => 'nullable|integer|exists:events,id',
            'transaction_id'         => 'nullable|string|max:255',

            'member_name'            => 'nullable|array',
            'member_name.*'          => 'nullable|string|max:255',
            'member_mobile'          => 'nullable|array',
            'member_mobile.*'        => 'nullable|string|max:30',
            'relation_id'            => 'nullable|array',
            'relation_id.*'          => 'nullable|integer|exists:relations,id',
            'dob'                    => 'nullable|array',
            'dob.*'                  => 'nullable|date',
            'age'                    => 'nullable|array',
            'age.*'                  => 'nullable|integer',
            'food_id'                => 'nullable|array',
            'food_id.*'              => 'nullable|integer|exists:foods,id',
            'amount'                 => 'nullable|array',
            'amount.*'               => 'nullable|numeric',
            'section_description'    => 'nullable|string',

            'section3_description'   => 'nullable|string',
            'member_first_name'      => 'nullable|string|max:255',
            'member_last_name'       => 'nullable|string|max:255',
            'award_name'             => 'nullable|string|max:255',
            'gender'                 => 'nullable|string|max:255',
            'award_type'             => 'nullable|string|in:certificate,award',
            'photo_attached'         => 'nullable|image|max:2048',
            'food_id_section3'       => 'nullable|integer|exists:foods,id',
            'relation_id_section3'   => 'nullable|integer|exists:relations,id',
            'amount_section3'        => 'nullable|numeric',
            'special_comment'        => 'nullable|string',

            'company_logo'           => 'nullable|image|max:2048',
            'qr_code'                => 'nullable|image|max:2048',
            'screenshot_payment'     => 'nullable|image|max:4096',
        ]);

        // --- Upload files (delete old ones if replaced) ---
        $files = $this->uploadFiles($request, [
            'company_logo', 'qr_code', 'screenshot_payment', 'photo_attached'
        ], $reg);

        // --- Build members ---
        $members        = $this->buildMembers($request);
        $amountSection3 = $this->calcAwardAmount($request);
        $grandTotal     = array_sum(array_column($members, 'amount')) + $amountSection3;

        // Helpful debug: log key inputs (exclude files) and computed values for update
        try {
            Log::debug('Registration update input', [
                'user_id' => auth()->id(),
                'registration_id' => $reg->id,
                'request' => $request->except(['company_logo','qr_code','screenshot_payment','photo_attached']),
            ]);
            Log::debug('Computed members and totals for registration update', [
                'members_count' => count($members),
                'members' => $members,
                'amount_section3' => $amountSection3,
                'grand_total' => $grandTotal,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to write debug log for registration update: '.$e->getMessage());
        }

        DB::beginTransaction();
        try {
            // 1. Update Registration
            $reg->update([
                'event_id'            => $request->input('event_id'),
                'chapter_id'          => $request->input('chapter_id'),
                'chain_name'          => $request->input('chain_name'),
                'company_name'        => $request->input('company_name'),
                'about_company'       => $request->input('about_company'),
                'company_logo'        => $files['company_logo'],
                'qr_code'             => $files['qr_code'],
                'screenshot_payment'  => $files['screenshot_payment'],
                'grand_total'         => $grandTotal,
                'transaction_id'      => $request->input('transaction_id'),
            ]);

            // 2. Sync Members — delete all old, re-insert fresh
            $reg->members()->delete();
            foreach ($members as $memberData) {
                Member::create(array_merge($memberData, ['registration_id' => $reg->id]));
            }

            // 3. Sync Award
            $awardData = [
                'section3_description' => $request->input('section3_description'),
                'first_name'           => $request->input('member_first_name'),
                'last_name'            => $request->input('member_last_name'),
                'gender'               => $request->input('gender'),
                'award_name'           => $request->input('award_name'),
                'award_type'           => $request->input('award_type'),
                'photo_attached'       => $files['photo_attached'],
                'food_id'              => $request->input('food_id_section3'),
                'relation_id'          => $request->input('relation_id_section3'),
                'amount_section3'      => $amountSection3,
                'special_comment'      => $request->input('special_comment'),
            ];

            if ($this->hasAwardData($request)) {
                Award::updateOrCreate(
                    ['registration_id' => $reg->id],
                    $awardData
                );
            } else {
                // Remove award if all section 3 fields are empty
                $reg->award()->delete();
            }

            DB::commit();
            Log::info('Registration updated', ['id' => $reg->id, 'user' => auth()->id()]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update registration', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $msg = config('app.debug') ? 'Failed to update registration: ' . $e->getMessage() : 'Failed to update registration.';
            return back()->withInput()->with('error', $msg);
        }

        return $this->isAdmin()
            ? redirect()->route('registrations.index')->withSuccess('Registration updated successfully.')
            : redirect()->route('user.dashboard')->withSuccess('Registration updated successfully.');
    }

    // ---------------------------------------------------------------
    // DELETE
    // ---------------------------------------------------------------
    public function destroy($id)
    {
        $reg = Registration::findOrFail($id);

        if (!$this->isAdmin() && $reg->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete files from storage
        foreach (['company_logo', 'qr_code', 'screenshot_payment'] as $f) {
            if ($reg->{$f}) {
                Storage::disk('public')->delete($reg->{$f});
            }
        }
        if ($reg->award?->photo_attached) {
            Storage::disk('public')->delete($reg->award->photo_attached);
        }

        $reg->delete(); // cascades members + award via DB foreign keys

        return redirect()->route('registrations.index')->withSuccess('Registration deleted.');
    }

    // ---------------------------------------------------------------
    // PRIVATE HELPERS
    // ---------------------------------------------------------------

    /**
     * Upload files and return their storage paths.
     * If $reg is passed, old files are deleted when replaced.
     */
    private function uploadFiles(Request $request, array $fields, ?Registration $reg = null): array
    {
        $result = [];
        foreach ($fields as $f) {
            if ($request->hasFile($f)) {
                // Delete old file if updating
                $oldPath = $reg?->{$f} ?? ($reg?->award?->photo_attached ?? null);
                if ($f === 'photo_attached') {
                    $oldPath = $reg?->award?->photo_attached;
                } else {
                    $oldPath = $reg?->{$f};
                }
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $result[$f] = $request->file($f)->store('registrations', 'public');
            } else {
                // Keep existing value
                if ($f === 'photo_attached') {
                    $result[$f] = $reg?->award?->photo_attached ?? null;
                } else {
                    $result[$f] = $reg?->{$f} ?? null;
                }
            }
        }
        return $result;
    }

    /**
     * Build members array from flat request inputs.
     * Server-side amount recalculation: age > 5 → 1000, else → 500.
     */
    private function buildMembers(Request $request): array
    {
        $members            = [];
        $names              = $request->input('member_name', []);
        $sectionDescription = $request->input('section_description'); // single field

        if (!is_array($names) || empty($names)) {
            return $members;
        }

        foreach ($names as $idx => $name) {
            $mobile = $request->input("member_mobile.{$idx}");
            if (empty($name) && empty($mobile)) {
                continue;
            }

            $age    = $request->input("age.{$idx}");
            $amount = ($age !== null && $age !== '')
                ? ((int) $age > 5 ? 1000 : 500)
                : (float) ($request->input("amount.{$idx}") ?? 0);

            $members[] = [
                'name'                => $name,
                'surname'             => $request->input("member_surname.{$idx}"),
                'mobile'              => $mobile,
                'relation_id'         => $request->input("relation_id.{$idx}"),
                'dob'                 => $request->input("dob.{$idx}"),
                'age'                 => $age,
                'food_id'             => $request->input("food_id.{$idx}"),
                'amount'              => $amount,
                'section_description' => $sectionDescription,
            ];
        }

        return $members;
    }

    /**
     * Calculate Section 3 award amount server-side.
     * certificate → 500, award → 1000, fallback to submitted value.
     */
    private function calcAwardAmount(Request $request): float
    {
        $map  = ['certificate' => 500, 'award' => 1000];
        $type = $request->input('award_type');
        return isset($map[$type])
            ? (float) $map[$type]
            : (float) ($request->input('amount_section3') ?? 0);
    }

    /**
     * Return true if any Section 3 field has a value.
     */
    private function hasAwardData(Request $request): bool
    {
        return $request->filled('award_name')
            || $request->filled('award_type')
            || $request->filled('section3_description')
            || $request->filled('member_first_name')
            || $request->hasFile('photo_attached');
    }
}