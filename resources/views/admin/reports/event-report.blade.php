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
                    <a href="{{ route('reports.event.exportExcel', array_filter(['event_id' => $selectedEvent->id, 'chapter_id' => request('chapter_id'), 'mobile' => request('mobile')])) }}"
                       class="btn btn-sm btn-save mr-2">
                        <i class="mdi mdi-file-excel mr-1"></i> Export Excel
                    </a>
                @else
                    <a href="{{ route('reports.event.exportExcel', array_filter(['chapter_id' => request('chapter_id'), 'mobile' => request('mobile')])) }}"
                       class="btn btn-sm btn-save mr-2">
                        <i class="mdi mdi-file-excel mr-1"></i> Export Excel
                    </a>
                @endif
            </div>
            @endif
        </div>

        {{-- ── Filter Form ── --}}
        <div class="card mb-3 shadow-sm no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('reports.event') }}" class="form-inline flex-wrap" style="gap:.5rem;">

                    <label class="mr-1 font-weight-600" style="font-size:.85rem;color:#251c4b;">
                        <i class="mdi mdi-calendar-search mr-1"></i> Event:
                    </label>
                    <select name="event_id" class="form-control mr-3" style="min-width:240px;" onchange="this.form.submit()">
                        <option value="">— All Events —</option>
                        @foreach($events as $e)
                            <option value="{{ $e->id }}"
                                {{ (isset($selectedEvent) && $selectedEvent?->id == $e->id) ? 'selected' : '' }}>
                                {{ $e->name }}
                                @if($e->date) ({{ \Carbon\Carbon::parse($e->date)->format('d M Y') }}) @endif
                            </option>
                        @endforeach
                    </select>

                    <label class="mr-1 font-weight-600" style="font-size:.85rem;color:#251c4b;">
                        <i class="mdi mdi-map-marker mr-1"></i> Chapter:
                    </label>
                    <select name="chapter_id" class="form-control mr-3" style="min-width:180px;">
                        <option value="">— All Chapters —</option>
                        @foreach($chapters as $c)
                            <option value="{{ $c->id }}"
                                {{ request('chapter_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="input-group mr-2" style="min-width:200px;">
                        <input type="text" name="mobile" class="form-control"
                               placeholder="Search mobile…" value="{{ request('mobile') }}">
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-outline-primary" type="submit">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($registrations->isNotEmpty())

        {{-- ── Summary Stats ── --}}
        @php
            $awardAmount = $registrations->flatMap(function($reg) { return $reg->awards; })->sum(function($a) { return $a->amount ?? 0; });
        @endphp
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

            <div class="col-6 col-md-3 mt-3 mt-md-0">
                <div class="stat-card" style="background:#b8860b;">
                    <div class="stat-label">Awards / Certs</div>
                    <div class="stat-value">{{ $stats['totalAwards'] }}</div>
                    <div class="stat-sub">Section 3 entries</div>
                </div>
            </div>

            <div class="col-6 col-md-3 mt-3 mt-md-0">
                <div class="stat-card" style="background:#1a7a4a;">
                    <div class="stat-label">Total Amount</div>
                    <div class="stat-value">
                        ₹{{ number_format($stats['totalAmount'] + $awardAmount, 2) }}
                    </div>
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
                <div class="d-flex flex-wrap" style="gap:.4rem;">
                    <strong class="mr-2" style="font-size:.82rem;">Members:</strong>
                    @foreach($stats['foodBreakdown'] as $food => $count)
                        <span class="food-pill">{{ $food }}: <strong>{{ $count }}</strong></span>
                    @endforeach

                    @if($stats['awardFoodBreakdown']->count())
                        <span class="mx-2 text-muted">|</span>
                        <strong class="mr-2" style="font-size:.82rem;">Awards:</strong>
                        @foreach($stats['awardFoodBreakdown'] as $food => $count)
                            <span class="food-pill" style="background:#fff3cd;color:#856404;">
                                {{ $food }}: <strong>{{ $count }}</strong>
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             SECTION 2 — Member Details
        ═══════════════════════════════════════════════════════════ --}}
        <div class="card mb-3 shadow-sm">
            <div class="section-header">
                <span class="section-number">2</span>
                <h6 class="section-title">Member Details</h6>
                <span class="ml-auto badge badge-secondary badge-pill">
                    {{ $stats['totalMembers'] }} Members across {{ $stats['totalRegistrations'] }} registrations
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead style="background:#f1f3f9;">
                        <tr>
                            <th>#</th>
                            <th>Company / PA Member</th>
                            <th>Chapter</th>
                            <th>Surname</th>
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
                                $members  = $reg->members;
                                $rowCount = max($members->count(), 1);
                                // PA member info comes from user relationship
                                $paName   = trim(($reg->user?->first_name ?? '') . ' ' . ($reg->user?->last_name ?? ''));
                                $paMobile = $reg->user?->mobile ?? '';
                            @endphp

                            @if($members->isEmpty())
                            <tr class="reg-row-first">
                                <td>{{ $regSerial }}</td>
                                <td>
                                    <div class="reg-company">{{ $reg->company_name ?? '—' }}</div>
                                    <div class="reg-meta">{{ $paName ?: '—' }}</div>
                                </td>
                                <td class="reg-meta">{{ $reg->chapter?->name ?? '—' }}</td>
                                <td colspan="8" class="text-muted" style="font-size:.78rem;">No members added</td>
                                <td>{{ $reg->transaction_id ?? '—' }}</td>
                                <td class="text-right font-weight-bold">₹{{ number_format($reg->grand_total, 2) }}</td>
                            </tr>
                            @else
                                @foreach($members as $mi => $member)
                                <tr class="{{ $mi === 0 ? 'reg-row-first' : '' }}">
                                    @if($mi === 0)
                                    <td rowspan="{{ $rowCount }}">{{ $regSerial }}</td>
                                    <td rowspan="{{ $rowCount }}">
                                        <div class="reg-company">{{ $reg->company_name ?? '—' }}</div>
                                        <div class="reg-meta">
                                            {{ $paName ?: '—' }}
                                            @if($paMobile)
                                                <span class="ml-1">· {{ $paMobile }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td rowspan="{{ $rowCount }}" class="reg-meta">{{ $reg->chapter?->name ?? '—' }}</td>
                                    @endif

                                    {{-- Member data — fields match members table --}}
                                    <td>{{ $member->surname ?? '—' }}</td>
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
                                    <td class="text-right">₹{{ number_format($member->amount ?? 0, 2) }}</td>

                                    @if($mi === 0)
                                    <td rowspan="{{ $rowCount }}" style="white-space:nowrap;">
                                        {{ $reg->transaction_id ?? '—' }}
                                    </td>
                                    <td rowspan="{{ $rowCount }}" class="text-right font-weight-bold" style="color:#251c4b;">
                                        ₹{{ number_format($reg->grand_total, 2) }}
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-4">No registrations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background:#f8f9fa;">
                        <tr>
                            <td colspan="10" class="text-right font-weight-bold" style="font-size:.82rem;">
                                Total Members: <strong>{{ $stats['totalMembers'] }}</strong>
                            </td>
                            <td class="text-right font-weight-bold" style="color:#1a7a4a;">—</td>
                            <td class="text-right font-weight-bold" style="font-size:.82rem;">Grand Total:</td>
                            <td class="text-right font-weight-bold" style="color:#1a7a4a;font-size:.95rem;">
                                ₹{{ number_format($stats['totalAmount'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             SECTION 3 — Awards Table (loop ALL awards per registration)
        ═══════════════════════════════════════════════════════════ --}}
        @php
            // Collect ALL individual award rows across all registrations
            $allAwards = $registrations->flatMap(function($reg) {
                return $reg->awards->map(function($a) use ($reg) {
                    $a->_reg = $reg; // attach parent reg for display
                    return $a;
                });
            });
        @endphp

        @if($allAwards->count())
        <div class="card mb-3 shadow-sm">
            <div class="section-header">
                <span class="section-number">3</span>
                <h6 class="section-title">Awards &amp; Certificates</h6>
                <span class="ml-auto badge badge-warning badge-pill">
                    {{ $allAwards->count() }} {{ $allAwards->count() === 1 ? 'Award' : 'Awards' }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead style="background:#fffbf0;">
                        <tr>
                            <th>#</th>
                            <th>Company / PA Member</th>
                            <th>Surname</th>
                            <th>Member Name</th>
                            <th>Gender</th>
                            <th>Department</th>
                            <th>Award Category</th>
                            <th>Award Type</th>
                            <th>Food</th>
                            <th class="text-right">Amount (₹)</th>
                            <th>Special Comment</th>
                            <th>Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allAwards as $i => $a)
                        @php $reg = $a->_reg; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="reg-company">{{ $reg->company_name ?? '—' }}</div>
                                <div class="reg-meta">
                                    {{ trim(($reg->user?->first_name ?? '') . ' ' . ($reg->user?->last_name ?? '')) ?: '—' }}
                                </div>
                            </td>
                            {{-- Award fields — matched to awards table columns --}}
                            <td>{{ $a->surname ?? '—' }}</td>
                            <td>{{ $a->first_name ?? '—' }}</td>
                            <td>
                                @if($a->gender)
                                    <span class="badge badge-light">{{ ucfirst($a->gender) }}</span>
                                @else —
                                @endif
                            </td>
                            <td>{{ $a->department ?? '—' }}</td>
                            <td>{{ $a->award_category ?? '—' }}</td>
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
                            <td class="text-right font-weight-bold">
                                ₹{{ number_format($a->amount ?? 0, 2) }}
                            </td>
                            <td style="font-size:.78rem;max-width:180px;">
                                {{ \Illuminate\Support\Str::limit($a->special_comment ?? '—', 80) }}
                            </td>
                            @php
                                $photoUrl = null;
                                if (!empty($a->photo_attached)) {
                                    if (\Illuminate\Support\Str::startsWith($a->photo_attached, ['http://', 'https://'])) {
                                        // If URL is same host, normalize scheme to current request to avoid mixed-content blocks
                                        $parsedHost = parse_url($a->photo_attached, PHP_URL_HOST);
                                        $currentHost = request()->getHost();
                                        if ($parsedHost && $parsedHost === $currentHost) {
                                            $scheme = request()->getScheme();
                                            $path = parse_url($a->photo_attached, PHP_URL_PATH) ?: '';
                                            $query = parse_url($a->photo_attached, PHP_URL_QUERY);
                                            $photoUrl = $scheme . '://' . $parsedHost . $path . ($query ? ('?' . $query) : '');
                                        } else {
                                            $photoUrl = $a->photo_attached;
                                        }
                                    } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists(ltrim($a->photo_attached, '/'))) {
                                        $photoUrl = \Illuminate\Support\Facades\Storage::url(ltrim($a->photo_attached, '/'));
                                    } else {
                                        $photoUrl = asset('storage/' . ltrim($a->photo_attached, '/'));
                                    }
                                }
                            @endphp
                            <td class="text-center">
                                @if($photoUrl)
                                    <a href="{{ $photoUrl }}" target="_blank" rel="noopener noreferrer">
                                        <i class="mdi mdi-image text-success"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#fffbf0;">
                        <tr>
                            <td colspan="9" class="text-right font-weight-bold" style="font-size:.82rem;">
                                Total Awards: <strong>{{ $allAwards->count() }}</strong>
                            </td>
                            <td class="text-right font-weight-bold" style="color:#856404;">
                                {{-- Use 'amount' column (correct field) --}}
                                ₹{{ number_format($allAwards->sum(fn($a) => $a->amount ?? 0), 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        @else
        {{-- No registrations --}}
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