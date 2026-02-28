<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Chapter;
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

        $events   = Event::orderByDesc('created_at')->get();
        $chapters = Chapter::orderBy('name')->get();

        $selectedEvent = null;
        $registrations = collect();
        $stats         = [];

        // ── Build query (shared for both event-filtered and all) ──────
        $regQuery = Registration::with([
            'user',
            'chapter',
            'members.relation',
            'members.food',
            'awards.food',          // awards is now HasMany
        ]);

        if ($request->filled('event_id')) {
            $selectedEvent = Event::findOrFail($request->event_id);
            $regQuery->where('event_id', $selectedEvent->id);
        }

        if ($request->filled('chapter_id')) {
            $regQuery->where('chapter_id', $request->chapter_id);
        }

        if ($request->filled('mobile')) {
            $mobile = $request->mobile;
            // Only match the registrant (PA member) mobile stored on the users table
            $regQuery->whereHas('user', function ($q) use ($mobile) {
                $q->where('mobile', 'like', "%{$mobile}%");
            });
        }

        $registrations = $regQuery->orderByDesc('created_at')->get();

        // ── Summary stats ─────────────────────────────────────────────
        $totalRegistrations = $registrations->count();
        $totalMembers       = $registrations->sum(fn($r) => $r->members->count());

        // Total individual award ROWS (not just registrations that have awards)
        $totalAwards        = $registrations->sum(fn($r) => $r->awards->count());

        // Grand total collected
        $totalAmount        = $registrations->sum('grand_total');

        // Food breakdown — members
        $foodBreakdown = $registrations
            ->flatMap(fn($r) => $r->members)
            ->groupBy(fn($m) => $m->food?->name ?? 'Not Selected')
            ->map(fn($group) => $group->count());

        // Food breakdown — awards (each award row, not just first)
        $awardFoodBreakdown = $registrations
            ->flatMap(fn($r) => $r->awards)
            ->filter(fn($a) => $a && $a->food)
            ->groupBy(fn($a) => $a->food->name)
            ->map(fn($group) => $group->count());

        $stats = compact(
            'totalRegistrations',
            'totalMembers',
            'totalAwards',
            'totalAmount',
            'foodBreakdown',
            'awardFoodBreakdown'
        );

        return view('admin.reports.event-report', compact(
            'events',
            'chapters',
            'selectedEvent',
            'registrations',
            'stats'
        ));
    }

    // ---------------------------------------------------------------
    // EXPORT EXCEL
    // ---------------------------------------------------------------
    public function exportExcel(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        $event = null;
        if ($request->filled('event_id')) {
            $request->validate(['event_id' => 'integer|exists:events,id']);
            $event = Event::findOrFail($request->event_id);
        }

        $regQuery = Registration::with([
            'user', 'chapter',
            'members.relation', 'members.food',
            'awards.food',
        ]);

        if ($event)                          $regQuery->where('event_id', $event->id);
        if ($request->filled('chapter_id'))  $regQuery->where('chapter_id', $request->chapter_id);
        if ($request->filled('mobile')) {
            $mobile = $request->mobile;
            // Only match the registrant (PA member) mobile for export
            $regQuery->whereHas('user', fn($q3) => $q3->where('mobile', 'like', "%{$mobile}%"));
        }

        $registrations = $regQuery->orderByDesc('created_at')->get();

        // ── Build workbook ───────────────────────────────────────────
        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet()->setTitle('Event Report');

        $style = fn($range, array $arr) => $ws->getStyle($range)->applyFromArray($arr);

        // All text BLACK (000000), all BG WHITE (FFFFFF) — no color
        $fnt = fn($bold = false, $sz = 9) => [
            'font' => [
                'name'  => 'Arial',
                'bold'  => $bold,
                'size'  => $sz,
                'color' => ['argb' => 'FF000000'],
            ],
        ];
        $aln = fn($h = 'left', $wrap = false) => [
            'alignment' => [
                'horizontal' => $h,
                'vertical'   => 'center',
                'wrapText'   => $wrap,
            ],
        ];
        $bdr = fn() => [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color'       => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $bgWhite = [
            'fill' => [
                'fillType'   => 'solid',
                'startColor' => ['argb' => 'FFFFFFFF'],
            ],
        ];

        // $set  — single cell, white bg, black text, border
        $set = function (string $cell, $val, string $h = 'left', bool $bold = false) use ($ws, $style, $fnt, $aln, $bdr, $bgWhite) {
            $ws->setCellValue($cell, $val ?? '');
            $style($cell, array_merge($bgWhite, $fnt($bold, 9), $aln($h), $bdr()));
        };

        // $mergeSet — merged rows, white bg, black text, border
        $mergeSet = function (string $col, int $r, int $rows, $val, string $h = 'left', bool $bold = false) use ($ws, $style, $fnt, $aln, $bdr, $bgWhite) {
            $range = $rows > 1 ? "{$col}{$r}:{$col}" . ($r + $rows - 1) : "{$col}{$r}";
            if ($rows > 1) $ws->mergeCells($range);
            $ws->setCellValue("{$col}{$r}", $val ?? '');
            $style($range, array_merge($bgWhite, $fnt($bold, 9), $aln($h), $bdr()));
        };

        // Column widths
        foreach ([
            'A' => 6,  'B' => 24, 'C' => 14, 'D' => 20, 'E' => 26,
            'F' => 22, 'G' => 22, 'H' => 18,
            'I' => 22, 'J' => 22, 'K' => 20, 'L' => 22,
            'M' => 22, 'N' => 10, 'O' => 20, 'P' => 20,
            'Q' => 14, 'R' => 32, 'S' => 24, 'T' => 10,
        ] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        // ── Row 1: Part headers (bold, white bg, black text) ─────────
        $ws->getRowDimension(1)->setRowHeight(22);
        foreach ([
            ['A1:H1', 'Part 1 — PA Member'],
            ['I1:L1', 'Part 2 — Business Partner / Guest / Family'],
            ['M1:T1', 'Part 3 — Team Member / Award'],
        ] as [$range, $text]) {
            $ws->mergeCells($range);
            $ws->setCellValue(explode(':', $range)[0], $text);
            $style($range, array_merge($bgWhite, $fnt(true, 10), $aln('center'), $bdr()));
        }

        // ── Row 2: Column headers (bold) ─────────────────────────────
        $ws->getRowDimension(2)->setRowHeight(30);
        foreach ([
            ['A2', 'Sr. No.'],
            ['B2', 'Company Name'],
            ['C2', 'Company Logo'],
            ['D2', 'Chapter Name'],
            ['E2', 'GA Member (Chain)'],
            ['F2', 'PA Member First Name'],
            ['G2', 'PA Member Last Name'],
            ['H2', 'Mobile Number'],
            ['I2', 'Member Name'],
            ['J2', 'Member Surname'],
            ['K2', 'Relation'],
            ['L2', 'Food Preference'],
            ['M2', 'Award Member Name'],
            ['N2', 'Award Member Surname'],
            ['O2', 'Gender'],
            ['P2', 'Department'],
            ['Q2', 'Award Type'],
            ['R2', 'Award Category'],
            ['S2', 'Special Comment'],
            ['T2', 'Amount (₹)'],
        ] as [$cell, $label]) {
            $ws->setCellValue($cell, $label);
            $style($cell, array_merge($bgWhite, $fnt(true, 8.5), $aln('center', true), $bdr()));
        }

        // ── Row 3: blank spacer (keep row height small) ───────────────
        // (removed — data starts directly at row 4 as requested)

        // ── Data rows start at ROW 4 ──────────────────────────────────
        $row = 4;
        $sr  = 1;

        foreach ($registrations as $reg) {
            $memberCount = $reg->members->count();
            $awardCount  = $reg->awards->count();
            $rowCount    = max($memberCount, $awardCount, 1);

            for ($ri = 0; $ri < $rowCount; $ri++) {
                $ws->getRowDimension($row + $ri)->setRowHeight(18);
            }

            // Part 1 — merged per registration, white bg black text
            $mergeSet('A', $row, $rowCount, $sr,                           'center');
            $mergeSet('B', $row, $rowCount, $reg->company_name);
            $mergeSet('C', $row, $rowCount, $reg->company_logo ?? '');
            $mergeSet('D', $row, $rowCount, $reg->chapter?->name);
            $mergeSet('E', $row, $rowCount, $reg->chain_name);
            $mergeSet('F', $row, $rowCount, $reg->user?->first_name ?? '');
            $mergeSet('G', $row, $rowCount, $reg->user?->last_name  ?? '');
            $mergeSet('H', $row, $rowCount, $reg->user?->mobile     ?? '', 'center');

            // Part 2 — one row per member
            foreach ($reg->members as $mi => $m) {
                $r = $row + $mi;
                $set('I' . $r, $m->name);
                $set('J' . $r, $m->surname ?? '');
                $set('K' . $r, $m->relation?->name ?? '');
                $set('L' . $r, $m->food?->name ?? '');
            }
            // Fill empty member rows with white+border
            for ($ri = $memberCount; $ri < $rowCount; $ri++) {
                foreach (['I', 'J', 'K', 'L'] as $c) {
                    $set($c . ($row + $ri), '');
                }
            }

            // Part 3 — one row per award
            foreach ($reg->awards as $ai => $a) {
                $r = $row + $ai;
                $set('M' . $r, $a->first_name    ?? '');
                $set('N' . $r, $a->surname       ?? '');
                $set('O' . $r, $a->gender        ?? '', 'center');
                $set('P' . $r, $a->department    ?? '');
                $set('Q' . $r, $a->award_type    ?? '', 'center');
                $set('R' . $r, $a->award_category ?? '');
                $set('S' . $r, $a->special_comment ?? '');
                $set('T' . $r, number_format($a->amount ?? 0, 2), 'right');
            }
            // Fill empty award rows with white+border
            for ($ri = $awardCount; $ri < $rowCount; $ri++) {
                foreach (['M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'] as $c) {
                    $set($c . ($row + $ri), '');
                }
            }

            $row += $rowCount;
            $sr++;
        }

        // Summary row
        $ws->getRowDimension($row)->setRowHeight(20);
        $ws->mergeCells("A{$row}:T{$row}");
        $totalMembersSum = $registrations->sum(fn($r) => $r->members->count());
        $totalAwardsSum  = $registrations->sum(fn($r) => $r->awards->count());
        $ws->setCellValue("A{$row}", "Total Registrations: {$registrations->count()}   |   Total Members: {$totalMembersSum}   |   Total Awards: {$totalAwardsSum}");
        $style("A{$row}:T{$row}", array_merge($bgWhite, $fnt(true, 9), $aln('center')));

        // Freeze header rows, page setup
        $ws->freezePane('A4');
        $ws->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToPage(true);
        $ws->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);

        // Stream
        $slug     = $event ? str_replace(' ', '-', strtolower($event->name)) : 'all-events';
        $filename = 'event-report-' . $slug . '-' . now()->format('Ymd') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($wb);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ---------------------------------------------------------------
    private function isAdmin(): bool
    {
        $user = auth()->user();
        return $user && (
            (isset($user->role) && $user->role === 'admin') ||
            (isset($user->email) && $user->email === 'superadmin@gmail.com')
        );
    }
}