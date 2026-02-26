<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Render the activity index view.
     */
    public function index()
    {
        return view('admin.activity.index');
    }

    /**
     * Return JSON for DataTables server-side processing.
     * Route: GET /activity/list  (name: activity.list)
     *
     * NOTE: This route MUST be declared BEFORE activity/{id} in web.php
     * to prevent Laravel matching "list" as the {id} wildcard.
     *   Route::get('activity/list', [...'list'])->name('activity.list');   // <-- first
     *   Route::get('activity/{id}', [...'show'])->name('activity.show');   // <-- second
     */
    public function list(Request $request)
    {
        try {
            $user = auth()->user();

            // Resolve table — prefer activity_log (Spatie), fallback to activities
            if (Schema::hasTable('activity_log')) {
                $table = 'activity_log';
            } elseif (Schema::hasTable('activities')) {
                $table = 'activities';
            } else {
                return response()->json([
                    'draw'            => (int) $request->query('draw', 0),
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                ]);
            }

            $isAdmin = $user && (
                (isset($user->role)  && $user->role  === 'admin') ||
                (isset($user->email) && $user->email === 'superadmin@gmail.com')
            );

            $query = DB::table($table);

            if (!$isAdmin) {
                $query->where('causer_id', auth()->id());
            }

            $totalRecords = $query->count();

            // ── Search ───────────────────────────────────────────────────
            // DataTables sends:  search[value]=xxx  as GET params
            $search = trim((string) $request->query('search')['value'] ?? '');
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('description',  'like', "%{$search}%")
                      ->orWhere('log_name',    'like', "%{$search}%")
                      ->orWhere('subject_type','like', "%{$search}%")
                      ->orWhere('causer_type', 'like', "%{$search}%");
                });
            }

            $filtered = $query->count();

            // ── Ordering ─────────────────────────────────────────────────
            // DataTables GET format: order[0][column]=4 & order[0][dir]=desc
            $columnMap = [
                'description' => 'description',
                'log_name'    => 'log_name',
                'created_at'  => 'created_at',
                // causer / subject are computed values — cannot be ordered by DB
            ];

            $orderArr         = $request->query('order', []);
            $orderColumnIndex = $orderArr[0]['column'] ?? 4;
            $orderDir         = in_array($orderArr[0]['dir'] ?? '', ['asc', 'desc'])
                                ? $orderArr[0]['dir'] : 'desc';

            $columnsArr   = $request->query('columns', []);
            $orderColData = $columnsArr[$orderColumnIndex]['data'] ?? 'created_at';
            $dbOrder      = $columnMap[$orderColData] ?? 'created_at';

            $query->orderBy($dbOrder, $orderDir);

            // ── Pagination ───────────────────────────────────────────────
            $start  = max(0, (int) $request->query('start', 0));
            $length = (int) $request->query('length', 25);
            if ($length < 1 || $length > 200) $length = 25;

            $rows = $query->skip($start)->take($length)->get();

            // ── Format rows ──────────────────────────────────────────────
            $data = $rows->map(function ($row) {
                $a = (array) $row;

                $causer = '-';
                if (!empty($a['causer_type'])) {
                    $causer = class_basename($a['causer_type']);
                    if (!empty($a['causer_id'])) {
                        $causer .= ' #' . $a['causer_id'];
                    }
                }

                $subject = '-';
                if (!empty($a['subject_type'])) {
                    $subject = class_basename($a['subject_type']);
                    if (!empty($a['subject_id'])) {
                        $subject .= ' #' . $a['subject_id'];
                    }
                }

                return [
                    'id'          => $a['id']          ?? null,
                    'description' => $a['description'] ?? '-',
                    'causer'      => $causer,
                    'subject'     => $subject,
                    'log_name'    => $a['log_name']    ?? '-',
                    'created_at'  => $a['created_at']  ?? null,
                ];
            })->values()->toArray();

            return response()->json([
                'draw'            => (int) $request->query('draw', 0),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filtered,
                'data'            => $data,
            ]);

        } catch (\Throwable $e) {
            Log::error('Activity list failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            return response()->json([
                'draw'            => (int) $request->query('draw', 0),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single activity detail.
     */
    public function show($id)
    {
        $activity = Activity::findOrFail($id);

        $user    = auth()->user();
        $isAdmin = $user && (
            (isset($user->role)  && $user->role  === 'admin') ||
            (isset($user->email) && $user->email === 'superadmin@gmail.com')
        );

        if (!$isAdmin) {
            if ($activity->causer_id !== auth()->id() && $activity->subject_id !== auth()->id()) {
                abort(403);
            }
        }

        $props = $activity->properties ?? [];
        $old   = [];
        $new   = [];

        if (is_array($props)) {
            if (isset($props['old'], $props['attributes'])) {
                $old = (array) $props['old'];
                $new = (array) $props['attributes'];
            } elseif (isset($props['old'], $props['new'])) {
                $old = (array) $props['old'];
                $new = (array) $props['new'];
            } else {
                $new = $props;
            }
        }

        $changes   = ['old' => $old, 'new' => $new];
        $model     = $activity->subject_type ?? 'N/A';
        $subjectId = $activity->subject_id;

        return view('admin.activity.show', compact('activity', 'changes', 'model', 'subjectId', 'id'));
    }
}