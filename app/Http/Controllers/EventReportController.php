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

        // All events and chapters for the dropdowns
        $events = Event::orderByDesc('created_at')->get();
        $chapters = Chapter::orderBy('name')->get();

        $selectedEvent  = null;
        $registrations  = collect();
        $stats          = [];

        if ($request->filled('event_id')) {
            $selectedEvent = Event::findOrFail($request->event_id);

            // Build registration query for this event
            $regQuery = Registration::where('event_id', $selectedEvent->id)
                ->with([
                    'user',
                    'chapter',
                    'members.relation',
                    'members.food',
                    'award.food',
                    'award.relation',
                ]);

            // Apply chapter filter when provided
            if ($request->filled('chapter_id')) {
                $regQuery->where('chapter_id', $request->chapter_id);
            }

            // Apply mobile search across members
            if ($request->filled('mobile')) {
                $mobile = $request->mobile;
                $regQuery->whereHas('members', function($q) use ($mobile) {
                    $q->where('mobile', 'like', "%{$mobile}%");
                });
            }

            $registrations = $regQuery->orderByDesc('created_at')->get();

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
            $regQuery = Registration::with([
                    'user',
                    'chapter',
                    'members.relation',
                    'members.food',
                    'award.food',
                    'award.relation',
                ]);

            if ($request->filled('chapter_id')) {
                $regQuery->where('chapter_id', $request->chapter_id);
            }

            if ($request->filled('mobile')) {
                $mobile = $request->mobile;
                $regQuery->whereHas('members', function($q) use ($mobile) {
                    $q->where('mobile', 'like', "%{$mobile}%");
                });
            }

            $registrations = $regQuery->orderByDesc('created_at')->get();

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
            'chapters',
            'selectedEvent',
            'registrations',
            'stats'
        ));
    }
    public function exportExcel(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        // event_id optional — if omitted, export all registrations
        if ($request->filled('event_id')) {
            $request->validate(['event_id' => 'integer|exists:events,id']);
            $event = Event::findOrFail($request->event_id);
        } else {
            $event = null;
        }

        $regQuery = Registration::with([
            'user', 'chapter', 'members.relation', 'members.food',
            'award.food', 'award.relation',
        ]);
        if ($event)                        $regQuery->where('event_id', $event->id);
        if ($request->filled('chapter_id')) $regQuery->where('chapter_id', $request->chapter_id);
        if ($request->filled('mobile')) {
            $mobile = $request->mobile;
            $regQuery->whereHas('members', fn($q) => $q->where('mobile', 'like', "%{$mobile}%"));
        }
        $registrations = $regQuery->orderByDesc('created_at')->get();

        // ── Build workbook ────────────────────────────────────────────────────
        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet()->setTitle('Event Report');

        // Shorthand helpers
        $style = fn($range, array $arr) => $ws->getStyle($range)->applyFromArray($arr);
        // Minimal styling: no background fills, black text, simple thin black borders
        $bg    = fn($hex = null)        => [];
        $fnt   = fn($hex = null, $bold = true, $sz = 9) => ['font' => ['name' => 'Arial', 'bold' => $bold, 'size' => $sz, 'color' => ['argb' => 'FF000000']]];
        $aln   = fn($h='left',$wrap=false) => ['alignment' => ['horizontal'=>$h,'vertical'=>'center','wrapText'=>$wrap]];
        $bdr   = fn($hex='000000') => ['borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['argb'=>'FF000000']]]];

        $set = function(string $cell, $val, string $bgHex, string $h='left', bool $wrap=false, bool $bold=false) use ($ws, $style, $bg, $fnt, $aln, $bdr) {
            $ws->setCellValue($cell, $val ?? '');
            $style($cell, array_merge($bg($bgHex), $fnt('111111',$bold,9), $aln($h,$wrap), $bdr()));
        };

        $mergeSet = function(string $col, int $r, int $rows, $val, string $bgHex, string $h='left') use ($ws, $style, $bg, $fnt, $aln, $bdr) {
            $range = $rows > 1 ? "{$col}{$r}:{$col}".($r+$rows-1) : "{$col}{$r}";
            if ($rows > 1) $ws->mergeCells($range);
            $ws->setCellValue("{$col}{$r}", $val ?? '');
            $style($range, array_merge($bg($bgHex), $fnt('111111',false,9), $aln($h), $bdr()));
        };

        // Column widths
        foreach ([
            'A'=>6,'B'=>24,'C'=>14,'D'=>20,'E'=>26,'F'=>22,'G'=>22,'H'=>18,'I'=>3,
            'J'=>22,'K'=>22,'L'=>20,'M'=>22,'N'=>3,'O'=>22,
            'P'=>10,'Q'=>20,'R'=>20,'S'=>14,'T'=>32,'U'=>24,'V'=>10,
        ] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        // Row 1: Title
        $eventLabel = $event ? $event->name : 'All Events';
        $ws->mergeCells('A1:V1');
        $ws->setCellValue('A1', 'EVENT ATTENDANCE & AWARD REPORT — '.strtoupper($eventLabel));
        $ws->getRowDimension(1)->setRowHeight(28);
        $style('A1:V1', array_merge($bg('251C4B'), $fnt('FFFFFF',true,13), $aln('center')));

        // Row 2: Meta
        $ws->mergeCells('A2:V2');
        $ws->getRowDimension(2)->setRowHeight(18);
        $style('A2:V2', array_merge($bg('EBEBEB'), $fnt('444444',false,8), $aln('center')));

        // Row 3: Spacer
        $ws->getRowDimension(3)->setRowHeight(5);
        $style('A3:V3', $bg('D5D0EA'));

        // Row 4: Part headers
        $ws->getRowDimension(4)->setRowHeight(26);
        foreach ([
            ['A4:H4', 'Part 1  ( PA Member )',                            '3B2F7F'],
            ['I4:I4', '',                                                  '251C4B'],
            ['J4:M4', 'Part 2  ( Business Partner / Guest / Family )',    '1F5C8B'],
            ['N4:N4', '',                                                  '251C4B'],
            ['O4:V4', 'Part 3  ( Team Member / Award )',                  '7B3F00'],
        ] as [$range, $text, $bgHex]) {
            $ws->mergeCells($range);
            $ws->setCellValue(explode(':', $range)[0], $text);
            $style($range, array_merge($bg($bgHex), $fnt('FFFFFF',true,11), $aln('center')));
        }

        // Row 5: Column headers
        $ws->getRowDimension(5)->setRowHeight(34);
        foreach ([
            ['A5','Sr. No.','C5B8F0','251C4B'],    ['B5','Company Name','C5B8F0','251C4B'],
            ['C5','Company Logo','C5B8F0','251C4B'], ['D5','Chapter Name','C5B8F0','251C4B'],
            ['E5','GA Member Name (Chain)','C5B8F0','251C4B'],['F5','PA Member Name','C5B8F0','251C4B'],
            ['G5','PA Member Surname','C5B8F0','251C4B'],['H5','Mobile Number','C5B8F0','251C4B'],
            ['I5','','251C4B','FFFFFF'],
            ['J5','Name','B8D6F0','1F5C8B'],['K5','Surname','B8D6F0','1F5C8B'],
            ['L5','Relation','B8D6F0','1F5C8B'],['M5','Food Preference','B8D6F0','1F5C8B'],
            ['N5','','251C4B','FFFFFF'],
            ['O5','Team Member Surname','F0D9B8','7B3F00'],['P5','Gender','F0D9B8','7B3F00'],
            ['Q5','Food Preference','F0D9B8','7B3F00'],['R5','Designation','F0D9B8','7B3F00'],
            ['S5','Award Type','F0D9B8','7B3F00'],['T5','Award Category (Title)','F0D9B8','7B3F00'],
            ['U5','Special Remarks','F0D9B8','7B3F00'],['V5','Photo','F0D9B8','7B3F00'],
        ] as [$cell, $label, $bgHex, $txtHex]) {
            $ws->setCellValue($cell, $label);
            $style($cell, array_merge($bg($bgHex), $fnt($txtHex,true,8.5), $aln('center',true), $bdr()));
        }

        // Data rows
        $row = 6;
        $sr  = 1;
        foreach ($registrations as $reg) {
            $memberCount = $reg->members->count();
            $rowCount    = max($memberCount, 1);
            for ($ri = 0; $ri < $rowCount; $ri++) $ws->getRowDimension($row+$ri)->setRowHeight(18);

            $b1 = $sr%2===0 ? 'F9F8FF' : 'EFECFF';
            $b2 = $sr%2===0 ? 'F5FAFF' : 'E8F3FF';
            $b3 = $sr%2===0 ? 'FFFAF3' : 'FFF4E5';
            $sp = 'D0CCE8';

            // Part 1 (merged per registration)
            $mergeSet('A',$row,$rowCount,$sr,                      $b1,'center');
            $mergeSet('B',$row,$rowCount,$reg->company_name,        $b1);
            $mergeSet('C',$row,$rowCount,$reg->company_logo ?? '',   $b1);
            $mergeSet('D',$row,$rowCount,$reg->chapter?->name,      $b1);
            $mergeSet('E',$row,$rowCount,$reg->chain_name,          $b1);
            $mergeSet('F',$row,$rowCount,$reg->first_name,          $b1);
            $mergeSet('G',$row,$rowCount,$reg->last_name,           $b1);
            $mergeSet('H',$row,$rowCount,$reg->mobile ?? '',        $b1,'center');
          

            for ($ri=0;$ri<$rowCount;$ri++) $ws->getStyle('I'.($row+$ri))->applyFromArray($bg($sp));

            // Part 2 (one row per member)
            foreach ($reg->members as $mi => $m) {
                $r = $row + $mi;
                $set('J'.$r, $m->name,             $b2);
                $set('K'.$r, $m->surname ?? '',    $b2);
                $set('L'.$r, $m->relation?->name,  $b2);
                $set('M'.$r, $m->food?->name,      $b2);
            }
            for ($ri=$memberCount;$ri<$rowCount;$ri++) {
                foreach(['J','K','L','M'] as $c) $ws->getStyle($c.($row+$ri))->applyFromArray($bg($b2));
            }

            for ($ri=0;$ri<$rowCount;$ri++) $ws->getStyle('N'.($row+$ri))->applyFromArray($bg($sp));

            // Part 3 (award, merged) — use full name (first + last)
            $a = $reg->award;
            $mergeSet('O',$row,$rowCount,trim(($a?->first_name ?? '') . ' ' . ($a?->last_name ?? '')),          $b3);
            $mergeSet('P',$row,$rowCount,'',                      $b3,'center');
            $mergeSet('Q',$row,$rowCount,$a?->food?->name,        $b3);
            $mergeSet('R',$row,$rowCount,$a?->relation?->name,    $b3);
            $mergeSet('S',$row,$rowCount,$a?->award_type,         $b3,'center');
            $mergeSet('T',$row,$rowCount,$a?->award_name,         $b3);
            $mergeSet('U',$row,$rowCount,$a?->special_comment,    $b3);
            $mergeSet('V',$row,$rowCount,$a?->photo_attached?'Yes':'No', $b3,'center');

            $row += $rowCount;
            $sr++;
        }

        // Summary row
        $ws->getRowDimension($row)->setRowHeight(20);
        $ws->mergeCells("A{$row}:V{$row}");
        $totalMembers = $registrations->sum(fn($r) => $r->members->count());
        $totalAwards  = $registrations->filter(fn($r) => $r->award)->count();
        $ws->setCellValue("A{$row}", "Total Registrations: {$registrations->count()}   |   Total Members: {$totalMembers}   |   Total Awards: {$totalAwards}");
        $style("A{$row}:V{$row}", array_merge($bg('251C4B'), $fnt('FFFFFF',true,9), $aln('center')));

        // Freeze panes + page setup
        $ws->freezePane('A6');
        $ws->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)->setFitToPage(true);
        $ws->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 5);

        // Stream
        $slug     = $event ? str_replace(' ','-',strtolower($event->name)) : 'all-events';
        $filename = 'event-report-'.$slug.'-'.now()->format('Ymd').'.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($wb);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
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