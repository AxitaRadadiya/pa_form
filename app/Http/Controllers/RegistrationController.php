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
        $reg = Registration::with(['members.relation', 'members.food', 'awards'])
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

        $event        = null;
        $qr_image_url = null;

        if ($request->has('event')) {
            $event = Event::find($request->get('event'));

            // Fetch QR code from the Event model and build full public URL
            if ($event && $event->qr_code) {
                $qr_image_url = asset('storage/' . $event->qr_code);
            }
        }

        return view('admin.eventsForm.create', compact('chapters', 'relations', 'foods', 'event', 'qr_image_url'));
    }

    // ---------------------------------------------------------------
    // STORE
    // ---------------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            // Section 1 — Personal / Company
            'first_name'             => 'nullable|string|max:255',
            'last_name'              => 'nullable|string|max:255',
            'mobile'                 => 'nullable|string|max:30',
            'email'                  => 'nullable|email|max:255',
            'chapter_id'             => 'required|integer|exists:chapters,id',
            'chain_name'             => 'required|string|max:255',
            'company_name'           => 'nullable|string|max:255',
            'about_company'          => 'nullable|string',
            'company_logo'           => 'nullable|image|max:2048',
            'event_id'               => 'nullable|integer|exists:events,id',

            // Section 2 — Members (arrays)
            'section_description'    => 'nullable|string',
            'member_surname'         => 'nullable|array',
            'member_surname.*'       => 'nullable|string|max:255',
            'member_name'            => 'nullable|array',
            'member_name.*'          => 'nullable|string|max:255',
            'member_mobile'          => 'nullable|array',
            'member_mobile.*'        => 'nullable|string|max:30',
            'relation_id'            => 'nullable|array',
            'relation_id.*'          => 'nullable|integer|exists:relations,id',
            'dob'                    => 'nullable|array',
            'dob.*'                  => 'nullable|date',
            'age'                    => 'nullable|array',
            'age.*'                  => 'nullable|integer|min:0|max:120',
            'food_id'                => 'nullable|array',
            'food_id.*'              => 'nullable|integer|exists:foods,id',
            'amount'                 => 'nullable|array',
            'amount.*'               => 'nullable|numeric',

            // Section 3 — Awards (arrays) — matches blade: award_member_surname[], award_member_name[], etc.
            'section3_description'        => 'nullable|string',
            'award_member_surname'        => 'nullable|array',
            'award_member_surname.*'      => 'nullable|string|max:255',
            'award_member_name'           => 'nullable|array',
            'award_member_name.*'         => 'nullable|string|max:255',
            'award_gender'                => 'nullable|array',
            'award_gender.*'              => 'nullable|string|in:male,female',
            'award_department'            => 'nullable|array',
            'award_department.*'          => 'nullable|string|max:255',
            'award_category'              => 'nullable|array',
            'award_category.*'            => 'nullable|string|max:255',
            'award_type'                  => 'nullable|array',
            'award_type.*'                => 'nullable|string|in:certificate,award',
            'award_food_id'               => 'nullable|array',
            'award_food_id.*'             => 'nullable|integer|exists:foods,id',
            'award_amount'                => 'nullable|array',
            'award_amount.*'              => 'nullable|numeric',
            'award_photo'                 => 'nullable|array',
            'award_photo.*'               => 'nullable|image|max:2048',
            'award_special_comment'       => 'nullable|array',
            'award_special_comment.*'     => 'nullable|string',

            // Payment
            'transaction_id'         => 'nullable|string|max:255',
            'grand_total'            => 'nullable|numeric',
            'screenshot_payment'     => 'nullable|image|max:4096',
            'qr_code'                => 'nullable|image|max:2048',
        ]);

        // --- Upload single files ---
        $files = $this->uploadFiles($request, [
            'company_logo', 'qr_code', 'screenshot_payment',
        ]);

        // --- Build members array (Section 2) ---
        $members = $this->buildMembers($request);

        // --- Build awards array (Section 3) ---
        $awards = $this->buildAwards($request);

        // --- Grand total (server-side recalc) ---
        $memberTotal = array_sum(array_column($members, 'amount'));
        $awardTotal  = array_sum(array_column($awards,  'amount'));
        $grandTotal  = $memberTotal + $awardTotal;

        Log::debug('Registration store input', [
            'user_id'        => auth()->id(),
            'members_count'  => count($members),
            'awards_count'   => count($awards),
            'grand_total'    => $grandTotal,
            'members'        => $members,
            'awards'         => $awards,
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Registration (Section 1)
            $reg = Registration::create([
                'user_id'            => auth()->id(),
                'event_id'           => $request->input('event_id') ?: null,
                'chapter_id'         => $request->input('chapter_id'),
                'chain_name'         => $request->input('chain_name'),
                'company_name'       => $request->input('company_name'),
                'about_company'      => $request->input('about_company'),
                'company_logo'       => $files['company_logo'] ?? null,
                'qr_code'            => $files['qr_code'] ?? null,
                'screenshot_payment' => $files['screenshot_payment'] ?? null,
                'grand_total'        => $grandTotal,
                'transaction_id'     => $request->input('transaction_id'),
            ]);

            // 2. Create Members (Section 2)
            foreach ($members as $memberData) {
                Member::create(array_merge($memberData, ['registration_id' => $reg->id]));
            }

            // 3. Create Awards (Section 3)
            foreach ($awards as $idx => $awardData) {
                // Handle per-award photo upload
                $photoPath = null;
                if ($request->hasFile("award_photo.{$idx}")) {
                    $photoPath = $request->file("award_photo.{$idx}")->store('registrations/awards', 'public');
                }
                Award::create(array_merge($awardData, [
                    'registration_id' => $reg->id,
                    'photo_attached'  => $photoPath,
                ]));
            }

            DB::commit();
            Log::info('Registration stored', ['id' => $reg->id, 'user' => auth()->id()]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $msg = config('app.debug')
                ? 'Failed to save registration: ' . $e->getMessage()
                : 'Failed to save registration.';
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
        $reg = Registration::with(['members', 'awards'])->findOrFail($id);

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
        $reg = Registration::with(['members', 'awards'])->findOrFail($id);

        if (!$this->isAdmin() && $reg->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'first_name'             => 'nullable|string|max:255',
            'last_name'              => 'nullable|string|max:255',
            'mobile'                 => 'nullable|string|max:30',
            'email'                  => 'nullable|email|max:255',
            'chapter_id'             => 'required|integer|exists:chapters,id',
            'chain_name'             => 'required|string|max:255',
            'company_name'           => 'nullable|string|max:255',
            'about_company'          => 'nullable|string',
            'company_logo'           => 'nullable|image|max:2048',
            'event_id'               => 'nullable|integer|exists:events,id',

            'section_description'    => 'nullable|string',
            'member_surname'         => 'nullable|array',
            'member_surname.*'       => 'nullable|string|max:255',
            'member_name'            => 'nullable|array',
            'member_name.*'          => 'nullable|string|max:255',
            'member_mobile'          => 'nullable|array',
            'member_mobile.*'        => 'nullable|string|max:30',
            'relation_id'            => 'nullable|array',
            'relation_id.*'          => 'nullable|integer|exists:relations,id',
            'dob'                    => 'nullable|array',
            'dob.*'                  => 'nullable|date',
            'age'                    => 'nullable|array',
            'age.*'                  => 'nullable|integer|min:0|max:120',
            'food_id'                => 'nullable|array',
            'food_id.*'              => 'nullable|integer|exists:foods,id',
            'amount'                 => 'nullable|array',
            'amount.*'               => 'nullable|numeric',

            'section3_description'        => 'nullable|string',
            'award_member_surname'        => 'nullable|array',
            'award_member_surname.*'      => 'nullable|string|max:255',
            'award_member_name'           => 'nullable|array',
            'award_member_name.*'         => 'nullable|string|max:255',
            'award_gender'                => 'nullable|array',
            'award_gender.*'              => 'nullable|string|in:male,female',
            'award_department'            => 'nullable|array',
            'award_department.*'          => 'nullable|string|max:255',
            'award_category'              => 'nullable|array',
            'award_category.*'            => 'nullable|string|max:255',
            'award_type'                  => 'nullable|array',
            'award_type.*'                => 'nullable|string|in:certificate,award',
            'award_food_id'               => 'nullable|array',
            'award_food_id.*'             => 'nullable|integer|exists:foods,id',
            'award_amount'                => 'nullable|array',
            'award_amount.*'              => 'nullable|numeric',
            'award_photo'                 => 'nullable|array',
            'award_photo.*'               => 'nullable|image|max:2048',
            'award_special_comment'       => 'nullable|array',
            'award_special_comment.*'     => 'nullable|string',

            'transaction_id'         => 'nullable|string|max:255',
            'grand_total'            => 'nullable|numeric',
            'screenshot_payment'     => 'nullable|image|max:4096',
            'qr_code'                => 'nullable|image|max:2048',
        ]);

        // --- Upload files (delete old ones if replaced) ---
        $files = $this->uploadFiles($request, [
            'company_logo', 'qr_code', 'screenshot_payment',
        ], $reg);

        // --- Build members & awards ---
        $members     = $this->buildMembers($request);
        $awards      = $this->buildAwards($request);
        $grandTotal  = array_sum(array_column($members, 'amount'))
                     + array_sum(array_column($awards,  'amount'));

        Log::debug('Registration update input', [
            'user_id'         => auth()->id(),
            'registration_id' => $reg->id,
            'members_count'   => count($members),
            'awards_count'    => count($awards),
            'grand_total'     => $grandTotal,
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Registration
            $reg->update([
                'event_id'           => $request->input('event_id') ?: null,
                'chapter_id'         => $request->input('chapter_id'),
                'chain_name'         => $request->input('chain_name'),
                'company_name'       => $request->input('company_name'),
                'about_company'      => $request->input('about_company'),
                'company_logo'       => $files['company_logo'],
                'qr_code'            => $files['qr_code'],
                'screenshot_payment' => $files['screenshot_payment'],
                'grand_total'        => $grandTotal,
                'transaction_id'     => $request->input('transaction_id'),
            ]);

            // 2. Sync Members — delete all old, re-insert fresh
            $reg->members()->delete();
            foreach ($members as $memberData) {
                Member::create(array_merge($memberData, ['registration_id' => $reg->id]));
            }

            // 3. Sync Awards — delete old award photos + records, re-insert fresh
            foreach ($reg->awards as $oldAward) {
                if ($oldAward->photo_attached) {
                    Storage::disk('public')->delete($oldAward->photo_attached);
                }
            }
            $reg->awards()->delete();

            foreach ($awards as $idx => $awardData) {
                $photoPath = null;
                if ($request->hasFile("award_photo.{$idx}")) {
                    $photoPath = $request->file("award_photo.{$idx}")->store('registrations/awards', 'public');
                }
                Award::create(array_merge($awardData, [
                    'registration_id' => $reg->id,
                    'photo_attached'  => $photoPath,
                ]));
            }

            DB::commit();
            Log::info('Registration updated', ['id' => $reg->id, 'user' => auth()->id()]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $msg = config('app.debug')
                ? 'Failed to update registration: ' . $e->getMessage()
                : 'Failed to update registration.';
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
        $reg = Registration::with('awards')->findOrFail($id);

        if (!$this->isAdmin() && $reg->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete single files from storage
        foreach (['company_logo', 'qr_code', 'screenshot_payment'] as $f) {
            if ($reg->{$f}) {
                Storage::disk('public')->delete($reg->{$f});
            }
        }

        // Delete per-award photos
        foreach ($reg->awards as $award) {
            if ($award->photo_attached) {
                Storage::disk('public')->delete($award->photo_attached);
            }
        }

        $reg->delete(); // cascades members + awards via DB foreign keys

        return redirect()->route('registrations.index')->withSuccess('Registration deleted.');
    }

    // ---------------------------------------------------------------
    // PRIVATE HELPERS
    // ---------------------------------------------------------------

    /**
     * Upload single-file fields and return their storage paths.
     * If $reg is passed, old files are deleted when replaced.
     */
    private function uploadFiles(Request $request, array $fields, ?Registration $reg = null): array
    {
        $result = [];
        foreach ($fields as $f) {
            if ($request->hasFile($f)) {
                if ($reg && $reg->{$f}) {
                    Storage::disk('public')->delete($reg->{$f});
                }
                $result[$f] = $request->file($f)->store('registrations', 'public');
            } else {
                $result[$f] = $reg?->{$f} ?? null;
            }
        }
        return $result;
    }

    /**
     * Build Section 2 members array from request.
     *
     * Amount rule:
     *   relation_id == 1  →  ₹ 0
     *   any other value   →  ₹ 600
     */
    private function buildMembers(Request $request): array
    {
        $members            = [];
        $names              = $request->input('member_name', []);
        $sectionDescription = $request->input('section_description');

        if (!is_array($names) || empty($names)) {
            return $members;
        }

        foreach ($names as $idx => $name) {
            $surname = $request->input("member_surname.{$idx}");
            $mobile  = $request->input("member_mobile.{$idx}");

            // Skip fully empty rows
            if (empty($name) && empty($surname) && empty($mobile)) {
                continue;
            }

            $relationId = $request->input("relation_id.{$idx}");

            // Server-side amount rule: relation_id=1 → ₹0, else → ₹600
            if ($relationId !== null && $relationId !== '') {
                $amount = ((int) $relationId === 1) ? 0 : 600;
            } else {
                // No relation selected — fall back to client-submitted value (0)
                $amount = (float) ($request->input("amount.{$idx}") ?? 0);
            }

            $members[] = [
                'surname'             => $surname,
                'name'                => $name,
                'mobile'              => $mobile,
                'relation_id'         => $relationId ?: null,
                'dob'                 => $request->input("dob.{$idx}") ?: null,
                'age'                 => $request->input("age.{$idx}") !== '' ? $request->input("age.{$idx}") : null,
                'food_id'             => $request->input("food_id.{$idx}") ?: null,
                'amount'              => $amount,
                'section_description' => $sectionDescription,
            ];
        }

        return $members;
    }

    /**
     * Build Section 3 awards array from request.
     *
     * Amount rule:
     *   award_type == 'certificate'  →  ₹ 400
     *   award_type == 'award'        →  ₹ 600
     *   not selected                 →  ₹ 0
     */
    private function buildAwards(Request $request): array
    {
        $awards              = [];
        $awardNames          = $request->input('award_member_name', []);
        $section3Description = $request->input('section3_description');

        if (!is_array($awardNames) || empty($awardNames)) {
            return $awards;
        }

        foreach ($awardNames as $idx => $name) {
            $surname = $request->input("award_member_surname.{$idx}");

            // Skip fully empty rows
            if (empty($name) && empty($surname)) {
                continue;
            }

            $awardType = $request->input("award_type.{$idx}");

            // Server-side amount rule
            if ($awardType === 'certificate') {
                $amount = 400;
            } elseif ($awardType === 'award') {
                $amount = 600;
            } else {
                $amount = 0;
            }

            $awards[] = [
                'section3_description' => $section3Description,
                'surname'              => $surname,
                'first_name'           => $name,
                'gender'               => $request->input("award_gender.{$idx}") ?: null,
                'department'           => $request->input("award_department.{$idx}"),
                'award_category'       => $request->input("award_category.{$idx}"),
                'award_type'           => $awardType ?: null,
                'food_id'              => $request->input("award_food_id.{$idx}") ?: null,
                'amount'               => $amount,
                'special_comment'      => $request->input("award_special_comment.{$idx}"),
                // photo_attached handled separately (file upload)
            ];
        }

        return $awards;
    }
}