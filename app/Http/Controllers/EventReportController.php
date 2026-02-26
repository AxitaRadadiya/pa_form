<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class EventReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Event-wise member attendance report (superadmin only)
     */
    public function index(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        // All events for the dropdown
        $events = Event::orderByDesc('created_at')->get();

        $selectedEvent  = null;
        $registrations  = collect();
        $stats          = [];

        if ($request->filled('event_id')) {
            $selectedEvent = Event::findOrFail($request->event_id);

            // Load all registrations for this event with members + award
            $registrations = Registration::where('event_id', $selectedEvent->id)
                ->with([
                    'user',
                    'chapter',
                    'members.relation',
                    'members.food',
                    'award.food',
                    'award.relation',
                ])
                ->orderByDesc('created_at')
                ->get();

            // Summary stats
            $totalMembers     = $registrations->sum(fn($r) => $r->members->count());
            $totalAwards      = $registrations->filter(fn($r) => $r->award)->count();
            $totalAmount      = $registrations->sum('grand_total');
            $totalRegistrations = $registrations->count();

            // Food preference breakdown (members)
            $foodBreakdown = $registrations
                ->flatMap(fn($r) => $r->members)
                ->groupBy(fn($m) => $m->food?->name ?? 'Not Selected')
                ->map(fn($group) => $group->count());

            // Food preference breakdown (awards)
            $awardFoodBreakdown = $registrations
                ->filter(fn($r) => $r->award && $r->award->food)
                ->groupBy(fn($r) => $r->award->food->name)
                ->map(fn($group) => $group->count());

            $stats = compact(
                'totalRegistrations',
                'totalMembers',
                'totalAwards',
                'totalAmount',
                'foodBreakdown',
                'awardFoodBreakdown'
            );
        }

        // If no event selected, load all registrations and compute overall stats
        if (! $request->filled('event_id')) {
            $registrations = Registration::with([
                    'user',
                    'chapter',
                    'members.relation',
                    'members.food',
                    'award.food',
                    'award.relation',
                ])
                ->orderByDesc('created_at')
                ->get();

            $totalMembers     = $registrations->sum(fn($r) => $r->members->count());
            $totalAwards      = $registrations->filter(fn($r) => $r->award)->count();
            $totalAmount      = $registrations->sum('grand_total');
            $totalRegistrations = $registrations->count();

            $foodBreakdown = $registrations
                ->flatMap(fn($r) => $r->members)
                ->groupBy(fn($m) => $m->food?->name ?? 'Not Selected')
                ->map(fn($group) => $group->count());

            $awardFoodBreakdown = $registrations
                ->filter(fn($r) => $r->award && $r->award->food)
                ->groupBy(fn($r) => $r->award->food->name)
                ->map(fn($group) => $group->count());

            $stats = compact(
                'totalRegistrations',
                'totalMembers',
                'totalAwards',
                'totalAmount',
                'foodBreakdown',
                'awardFoodBreakdown'
            );
        }

        return view('admin.reports.event-report', compact(
            'events',
            'selectedEvent',
            'registrations',
            'stats'
        ));
    }

    /**
     * Export event report as CSV
     */
    public function export(Request $request)
    {
        abort_unless($this->isAdmin(), 403);
        $request->validate(['event_id' => 'required|integer|exists:events,id']);

        $event = Event::findOrFail($request->event_id);

        $registrations = Registration::where('event_id', $event->id)
            ->with(['user', 'chapter', 'members.relation', 'members.food', 'award'])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'event-report-' . str_replace(' ', '-', strtolower($event->name)) . '-' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($registrations, $event) {
            $handle = fopen('php://output', 'w');

            // Event info header
            fputcsv($handle, ['Event Report: ' . $event->name]);
            fputcsv($handle, ['Generated:', now()->format('d M Y, h:i A')]);
            fputcsv($handle, []);

            // ── Section 2: Members ──
            fputcsv($handle, ['=== MEMBER DETAILS (SECTION 2) ===']);
            fputcsv($handle, [
                'Reg #', 'Company', 'Chapter', 'Registered By',
                'Member Name', 'Mobile', 'Relation', 'DOB', 'Age', 'Food', 'Amount (₹)',
                'Transaction ID', 'Grand Total (₹)',
            ]);

            foreach ($registrations as $reg) {
                if ($reg->members->isEmpty()) {
                    fputcsv($handle, [
                        $reg->id, $reg->company_name, $reg->chapter?->name, $reg->user?->name,
                        '—', '—', '—', '—', '—', '—', '—',
                        $reg->transaction_id, $reg->grand_total,
                    ]);
                } else {
                    foreach ($reg->members as $i => $member) {
                        fputcsv($handle, [
                            $i === 0 ? $reg->id : '',
                            $i === 0 ? $reg->company_name : '',
                            $i === 0 ? $reg->chapter?->name : '',
                            $i === 0 ? $reg->user?->name : '',
                            $member->name,
                            $member->mobile,
                            $member->relation?->name,
                            $member->dob,
                            $member->age,
                            $member->food?->name,
                            $member->amount,
                            $i === 0 ? $reg->transaction_id : '',
                            $i === 0 ? $reg->grand_total : '',
                        ]);
                    }
                }
            }

            fputcsv($handle, []);

            // ── Section 3: Awards ──
            fputcsv($handle, ['=== AWARD DETAILS (SECTION 3) ===']);
            fputcsv($handle, [
                'Reg #', 'Company', 'Award Name', 'First Name', 'Last Name',
                'Award Type', 'Food', 'Relation', 'Amount (₹)',
            ]);

            foreach ($registrations->filter(fn($r) => $r->award) as $reg) {
                $a = $reg->award;
                fputcsv($handle, [
                    $reg->id, $reg->company_name,
                    $a->award_name, $a->first_name, $a->last_name,
                    $a->award_type, $a->food?->name, $a->relation?->name,
                    $a->amount_section3,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function isAdmin(): bool
    {
        $user = auth()->user();
        return $user && (
            (isset($user->role) && $user->role === 'admin') ||
            (isset($user->email) && $user->email === 'superadmin@gmail.com')
        );
    }
}