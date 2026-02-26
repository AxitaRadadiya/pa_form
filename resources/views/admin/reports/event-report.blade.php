@extends('admin.layouts.app')
@section('title', 'Event-wise Member Report')

@section('content')
<style>
    .stat-card {
        border-radius: .4rem;
        padding: 1rem 1.2rem;
        color: #fff;
        margin-bottom: 1rem;
    }
    .stat-card .stat-label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        opacity: .8;
        font-weight: 600;
    }
    .stat-card .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1.1;
        margin-top: .2rem;
    }
    .stat-card .stat-sub {
        font-size: .72rem;
        opacity: .7;
        margin-top: .15rem;
    }
    .section-header {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .65rem 1.1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: .25rem .25rem 0 0;
    }
    .section-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px; height: 24px;
        border-radius: 50%;
        background: #251c4b;
        color: #fff;
        font-size: .7rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .section-title {
        font-size: .88rem;
        font-weight: 600;
        color: #251c4b;
        margin: 0;
    }
    .reg-company {
        font-weight: 600;
        color: #251c4b;
        font-size: .82rem;
    }
    .reg-meta {
        font-size: .74rem;
        color: #6c757d;
    }
    .badge-food {
        background: #e8f4fd;
        color: #1a6fa8;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: .72rem;
        font-weight: 600;
    }
    .badge-award {
        background: #fff3cd;
        color: #856404;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: .72rem;
        font-weight: 600;
    }
    .table th {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #495057;
        font-weight: 700;
        white-space: nowrap;
    }
    .table td {
        font-size: .82rem;
        vertical-align: middle;
    }
    .reg-row-first td {
        border-top: 2px solid #dee2e6 !important;
        background: #fafbff;
    }
    .no-data {
        text-align: center;
        padding: 3rem 1rem;
        color: #adb5bd;
    }
    .no-data i { font-size: 2.5rem; display: block; margin-bottom: .5rem; }
    .food-pill {
        display: inline-block;
        background: #f1f3f5;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: .72rem;
        color: #495057;
        font-weight: 500;
        margin: 1px;
    }
    @media print {
        .no-print { display: none !important; }
        .card { border: 1px solid #dee2e6 !important; }
    }
</style>

<div class="row">
    <div class="col-12">

        {{-- ── Page Header ── --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-0" style="color:#251c4b;font-weight:700;">
                    <i class="mdi mdi-chart-bar mr-2"></i>Event-wise Member Report
                </h4>
                <small class="text-muted">Select an event to view all registrations and members</small>
            </div>
            @if($registrations->isNotEmpty())
            <div class="no-print">
                @if(isset($selectedEvent) && $selectedEvent)
                    <a href="{{ route('reports.event.export', ['event_id' => $selectedEvent->id]) }}"
                       class="btn btn-sm btn-success mr-2">
                        <i class="mdi mdi-file-excel mr-1"></i> Export CSV
                    </a>
                @endif
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                    <i class="mdi mdi-printer mr-1"></i> Print
                </button>
            </div>
            @endif
        </div>

        {{-- ── Event Filter Form ── --}}
        <div class="card mb-3 shadow-sm no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('reports.event') }}" class="form-inline">
                    <label class="mr-2 font-weight-600" style="font-size:.85rem;color:#251c4b;">
                        <i class="mdi mdi-calendar-search mr-1"></i> Select Event:
                    </label>
                    <select name="event_id" class="form-control mr-3" style="min-width:260px;" onchange="this.form.submit()">
                        <option value="">— Choose an Event —</option>
                        @foreach($events as $e)
                            <option value="{{ $e->id }}"
                                {{ (isset($selectedEvent) && $selectedEvent?->id == $e->id) ? 'selected' : '' }}>
                                {{ $e->name }}
                                @if($e->date) ({{ \Carbon\Carbon::parse($e->date)->format('d M Y') }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm" style="background:#251c4b;color:#fff;">
                        <i class="mdi mdi-magnify mr-1"></i> View Report
                    </button>
                </form>
            </div>
        </div>

        @if($registrations->isNotEmpty())

        {{-- ── Summary Stats ── --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3">
                <div class="stat-card" style="background:#251c4b;">
                    <div class="stat-label">Total Registrations</div>
                    <div class="stat-value">{{ $stats['totalRegistrations'] }}</div>
                    <div class="stat-sub">Companies / families</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card" style="background:#1a7abf;">
                    <div class="stat-label">Total Members</div>
                    <div class="stat-value">{{ $stats['totalMembers'] }}</div>
                    <div class="stat-sub">Attending (Section 2)</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card" style="background:#b8860b;">
                    <div class="stat-label">Awards / Certs</div>
                    <div class="stat-value">{{ $stats['totalAwards'] }}</div>
                    <div class="stat-sub">Section 3 entries</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card" style="background:#1a7a4a;">
                    <div class="stat-label">Total Amount</div>
                    <div class="stat-value">₹{{ number_format($stats['totalAmount'], 0) }}</div>
                    <div class="stat-sub">Grand total collected</div>
                </div>
            </div>
        </div>

        {{-- ── Food Breakdown ── --}}
        @if($stats['foodBreakdown']->count())
        <div class="card mb-3 shadow-sm">
            <div class="section-header">
                <span class="section-number"><i class="mdi mdi-food" style="font-size:.7rem;"></i></span>
                <h6 class="section-title">Food Preference Breakdown</h6>
            </div>
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-2">
                    <strong class="mr-2" style="font-size:.82rem;">Members:</strong>
                    @foreach($stats['foodBreakdown'] as $food => $count)
                        <span class="food-pill">{{ $food }}: <strong>{{ $count }}</strong></span>
                    @endforeach

                    @if($stats['awardFoodBreakdown']->count())
                        <span class="mx-2 text-muted">|</span>
                        <strong class="mr-2" style="font-size:.82rem;">Awards:</strong>
                        @foreach($stats['awardFoodBreakdown'] as $food => $count)
                            <span class="food-pill" style="background:#fff3cd;color:#856404;">{{ $food }}: <strong>{{ $count }}</strong></span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             SECTION 2 — Member Details Table
        ═══════════════════════════════════════════════════════════ --}}
        <div class="card mb-3 shadow-sm">
            <div class="section-header">
                <span class="section-number">2</span>
                <h6 class="section-title">Member Details</h6>
                <span class="ml-auto badge badge-secondary badge-pill">
                    {{ $stats['totalMembers'] }} Members across {{ $stats['totalRegistrations'] }} registrations
                </span>
            </div>

            @if($registrations->isEmpty())
                <div class="no-data">
                    <i class="mdi mdi-account-off"></i>
                    No registrations found for this event.
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead style="background:#f1f3f9;">
                        <tr>
                            <th>#</th>
                            <th>Company / Registered By</th>
                            <th>Chapter</th>
                            <th>Member Name</th>
                            <th>Mobile</th>
                            <th>Relation</th>
                            <th>DOB</th>
                            <th>Age</th>
                            <th>Food</th>
                            <th class="text-right">Amount (₹)</th>
                            <th>Transaction ID</th>
                            <th class="text-right">Grand Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $regSerial = 0; @endphp
                        @forelse($registrations as $reg)
                            @php
                                $regSerial++;
                                $members = $reg->members;
                                $rowCount = max($members->count(), 1);
                            @endphp

                            @if($members->isEmpty())
                            <tr class="reg-row-first">
                                <td>{{ $regSerial }}</td>
                                <td>
                                    <div class="reg-company">{{ $reg->company_name ?? '—' }}</div>
                                    <div class="reg-meta">{{ $reg->user?->name }}</div>
                                </td>
                                <td class="reg-meta">{{ $reg->chapter?->name ?? '—' }}</td>
                                <td colspan="7" class="text-muted" style="font-size:.78rem;">No members added</td>
                                <td>{{ $reg->transaction_id ?? '—' }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($reg->grand_total, 2) }}</td>
                            </tr>
                            @else
                                @foreach($members as $mi => $member)
                                <tr class="{{ $mi === 0 ? 'reg-row-first' : '' }}">
                                    @if($mi === 0)
                                    <td rowspan="{{ $rowCount }}">{{ $regSerial }}</td>
                                    <td rowspan="{{ $rowCount }}">
                                        <div class="reg-company">{{ $reg->company_name ?? '—' }}</div>
                                        <div class="reg-meta">{{ $reg->user?->name }}</div>
                                    </td>
                                    <td rowspan="{{ $rowCount }}" class="reg-meta">{{ $reg->chapter?->name ?? '—' }}</td>
                                    @endif
                                    <td>{{ $member->name ?? '—' }}</td>
                                    <td>{{ $member->mobile ?? '—' }}</td>
                                    <td>
                                        @if($member->relation)
                                            <span class="badge badge-light">{{ $member->relation->name }}</span>
                                        @else —
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;">{{ $member->dob ?? '—' }}</td>
                                    <td class="text-center">{{ $member->age ?? '—' }}</td>
                                    <td>
                                        @if($member->food)
                                            <span class="badge-food">{{ $member->food->name }}</span>
                                        @else —
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($member->amount, 2) }}</td>
                                    @if($mi === 0)
                                    <td rowspan="{{ $rowCount }}" style="white-space:nowrap;">{{ $reg->transaction_id ?? '—' }}</td>
                                    <td rowspan="{{ $rowCount }}" class="text-right font-weight-bold" style="color:#251c4b;">
                                        ₹{{ number_format($reg->grand_total, 2) }}
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">No registrations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background:#f8f9fa;">
                        <tr>
                            <td colspan="9" class="text-right font-weight-bold" style="font-size:.82rem;">
                                Total Members: <strong>{{ $stats['totalMembers'] }}</strong>
                            </td>
                            <td class="text-right font-weight-bold" style="color:#1a7a4a;">
                                —
                            </td>
                            <td class="text-right font-weight-bold" style="font-size:.82rem;">Grand Total:</td>
                            <td class="text-right font-weight-bold" style="color:#1a7a4a;font-size:.95rem;">
                                ₹{{ number_format($stats['totalAmount'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             SECTION 3 — Awards Table
        ═══════════════════════════════════════════════════════════ --}}
        @php $awardRegs = $registrations->filter(fn($r) => $r->award); @endphp
        @if($awardRegs->count())
        <div class="card mb-3 shadow-sm">
            <div class="section-header">
                <span class="section-number">3</span>
                <h6 class="section-title">Awards &amp; Certificates</h6>
                <span class="ml-auto badge badge-warning badge-pill">
                    {{ $awardRegs->count() }} Awards
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead style="background:#fffbf0;">
                        <tr>
                            <th>#</th>
                            <th>Company</th>
                            <th>Member Name</th>
                            <th>Award Certificate</th>
                            <th>Type</th>
                            <th>Food</th>
                            <th>Relation</th>
                            <th class="text-right">Amount (₹)</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($awardRegs as $i => $reg)
                        @php $a = $reg->award; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="reg-company">{{ $reg->company_name ?? '—' }}</div>
                                <div class="reg-meta">{{ $reg->user?->name }}</div>
                            </td>
                            <td>
                                {{ trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) ?: '—' }}
                            </td>
                            <td>
                                {{ $a->award_name === 'Other' ? ($a->other_award_name ?? 'Other') : ($a->award_name ?? '—') }}
                            </td>
                            <td>
                                @if($a->award_type)
                                    <span class="badge-award">{{ ucfirst($a->award_type) }}</span>
                                @else —
                                @endif
                            </td>
                            <td>
                                @if($a->food)
                                    <span class="badge-food">{{ $a->food->name }}</span>
                                @else —
                                @endif
                            </td>
                            <td>{{ $a->relation?->name ?? '—' }}</td>
                            <td class="text-right font-weight-bold">
                                ₹{{ number_format($a->amount_section3 ?? 0, 2) }}
                            </td>
                            <td style="font-size:.78rem;max-width:200px;">
                                {{ \Illuminate\Support\Str::limit($a->section3_description ?? '—', 80) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#fffbf0;">
                        <tr>
                            <td colspan="7" class="text-right font-weight-bold" style="font-size:.82rem;">
                                Total Awards: <strong>{{ $awardRegs->count() }}</strong>
                            </td>
                            <td class="text-right font-weight-bold" style="color:#856404;">
                                ₹{{ number_format($awardRegs->sum(fn($r) => $r->award->amount_section3 ?? 0), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        @else
        {{-- No registrations yet --}}
        <div class="card shadow-sm">
            <div class="no-data" style="padding:4rem;">
                <i class="mdi mdi-calendar-search" style="font-size:3rem;color:#dee2e6;display:block;margin-bottom:.75rem;"></i>
                <div style="font-size:1rem;color:#adb5bd;font-weight:500;">No registrations found.</div>
                <div class="small text-muted mt-1">Select an event above to filter the report</div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
