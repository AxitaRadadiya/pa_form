@extends('admin.layouts.app')
@section('title', 'Register Member')

@section('content')

<style>
    .section-header {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .75rem 1.25rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: .25rem .25rem 0 0;
    }
    .section-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px; height: 26px;
        border-radius: 50%;
        background: #251c4b;
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .section-title {
        font-size: .9rem;
        font-weight: 600;
        color: #251c4b;
        margin: 0;
    }
    .section-badge { margin-left: auto; }

    .member-block, .award-block {
        border: 1px solid #dee2e6;
        border-left: 3px solid #251c4b;
        border-radius: .25rem;
        padding: 1rem;
        margin-bottom: .85rem;
        background: #fdfdff;
        transition: box-shadow .15s;
    }
    .award-block { border-left-color: #e6a817; }
    .member-block:hover, .award-block:hover {
        box-shadow: 0 2px 8px rgba(37,28,75,.1);
    }
    .block-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .75rem;
        padding-bottom: .6rem;
        border-bottom: 1px solid #eee;
    }
    .block-label {
        font-size: .75rem;
        font-weight: 700;
    }
    .form-label {
        font-weight: 500;
        font-size: .82rem;
        color: #495057;
        margin-bottom: .3rem;
    }
    .btn-add-row {
        background: #251c4b;
        color: #fff;
        border: none;
        font-size: .8rem;
        padding: .35rem .85rem;
        border-radius: .2rem;
    }
    .btn-add-row:hover { background: #3b2d74; color: #fff; }
    .btn-remove-row {
        background: transparent;
        border: 1px solid #dc3545;
        color: #dc3545;
        font-size: .75rem;
        padding: .25rem .6rem;
        border-radius: .2rem;
        cursor: pointer;
    }
    .btn-remove-row:hover { background: #dc3545; color: #fff; }
    .grand-total-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: linear-gradient(135deg,#251c4b,#3b2d74);
        color: #fff;
        border-radius: .35rem;
        padding: .85rem 1.25rem;
        margin-bottom: 1rem;
    }
    .grand-total-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; opacity: .8; }
    .grand-total-value { margin-left: auto; font-size: 1.6rem; font-weight: 700; }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8 d-flex">
        <div class="card w-100">

            {{-- Card Header --}}
            <div class="card-header d-flex align-items-center justify-content-between"
                 style="background:#251c4b; border-radius:.25rem .25rem 0 0;">
                <h4 class="card-title text-white mb-0">
                    <i class="mdi mdi-account-plus mr-2"></i> Register Member
                </h4>
                @if(isset($event) && $event)
                    <span class="badge badge-info badge-pill px-3 py-2">
                        Event: {{ $event->name }}
                    </span>
                @endif
            </div>

            <div class="card-body">

                {{-- Event Banner --}}
                @if(isset($event) && $event)
                <div class="alert alert-info d-flex justify-content-between align-items-center mb-4" role="alert">
                    <div>
                        <strong><i class="mdi mdi-calendar-check mr-1"></i> Event:</strong>
                        {{ $event->name }}
                        @if($event->description)
                            <div class="small mt-1">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</div>
                        @endif
                    </div>
                    <a href="{{ url('registrations/create') }}" class="btn btn-sm btn-outline-secondary ml-3">
                        <i class="mdi mdi-close"></i> Remove
                    </a>
                </div>
                @endif

                <form method="POST" action="{{ route('registrations.store') }}" enctype="multipart/form-data" id="reg_form">
                    <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
                    @csrf

                    {{-- ══════════════════════════════════════
                         SECTION 1 — Personal / Company
                    ══════════════════════════════════════ --}}
                    <div class="card mb-2 shadow-sm">
                        <div class="section-header">
                            <span class="section-number">1</span>
                            <h6 class="section-title">Personal / Company</h6>
                            <span class="section-badge">
                                <span class="badge badge-primary badge-pill">Basic Info</span>
                            </span>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name"
                                               value="{{ old('first_name', auth()->user()->first_name ?? '') }}"
                                               placeholder="First name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name"
                                               value="{{ old('last_name', auth()->user()->last_name ?? '') }}"
                                               placeholder="Last name">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">Mobile Number</label>
                                        <input type="tel" class="form-control" name="mobile"
                                               value="{{ old('mobile', auth()->user()->mobile ?? '') }}"
                                               placeholder="+91 00000 00000">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" name="email"
                                               value="{{ old('email', auth()->user()->email ?? '') }}"
                                               placeholder="you@example.com">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">Chapter Name <span class="text-danger">*</span></label>
                                        <select name="chapter_id" class="form-control" required>
                                            <option value="">— Select Chapter —</option>
                                            @if(isset($chapters))
                                                @foreach($chapters as $c)
                                                    <option value="{{ $c->id }}" {{ old('chapter_id') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">GA-Chain Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="chain_name"
                                               value="{{ old('chain_name') }}" placeholder="Chain name" required>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-2">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_name"
                                               value="{{ old('company_name') }}" placeholder="ABC Pvt. Ltd." required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-0">
                                        <label class="form-label">About Company</label>
                                        <textarea class="form-control" name="about_company" rows="2"
                                                  placeholder="Brief description of the company…">{{ old('about_company') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label">Company Logo <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control-file" name="company_logo" accept="image/*" required>
                                        <img id="company_logo_preview" class="img-thumb img-fluid mt-1" src="#" alt="Logo" style="display:none;max-height:60px;">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ══════════════════════════════════════
                         SECTION 2 — Member Details (Dynamic)
                    ══════════════════════════════════════ --}}
                    <div class="card mb-2 shadow-sm">
                        <div class="section-header">
                            <span class="section-number">2</span>
                            <h6 class="section-title">Member Details</h6>
                            <span class="section-badge">
                                <span class="badge badge-secondary badge-pill" id="member_count_badge">1 Member</span>
                            </span>
                        </div>
                        <div class="card-body">

                            <div class="form-group mb-2">
                                <label class="form-label">Section Description</label>
                                <textarea class="form-control" name="section_description" rows="2"
                                          placeholder="Optional notes for this section…">{{ old('section_description') }}</textarea>
                            </div>

                            <div id="members_container"></div>

                            <button type="button" class="btn btn-add-row" id="add_member_btn">
                                <i class="mdi mdi-plus-circle mr-1"></i> Add Member
                            </button>

                        </div>
                    </div>

                    {{-- ══════════════════════════════════════
                         SECTION 3 — Awards & Member Details (Dynamic)
                    ══════════════════════════════════════ --}}
                    <div class="card mb-2 shadow-sm">
                        <div class="section-header">
                            <span class="section-number">3</span>
                            <h6 class="section-title">Awards &amp; Member Details</h6>
                            <span class="section-badge">
                                <span class="badge badge-warning badge-pill" id="award_count_badge">1 Award</span>
                            </span>
                        </div>
                        <div class="card-body">

                            <div class="form-group mb-2">
                                <label class="form-label">Section Description</label>
                                <textarea class="form-control" name="section3_description" rows="2"
                                          placeholder="Optional notes…">{{ old('section3_description') }}</textarea>
                            </div>

                            <div id="awards_container"></div>

                            <button type="button" class="btn btn-add-row" id="add_award_btn"
                                    style="background:#e6a817; border-color:#e6a817;">
                                <i class="mdi mdi-plus-circle mr-1"></i> Add Award
                            </button>

                        </div>
                    </div>

                    {{-- ══════════════════════════════════════
                         PAYMENT & SUBMIT
                    ══════════════════════════════════════ --}}
                    <div class="card mb-2 shadow-sm">
                        <div class="card-body">

                            <div class="grand-total-box">
                                <div>
                                    <div class="grand-total-label">Grand Total</div>
                                    <small style="opacity:.6;">Auto-calculated from all members + awards</small>
                                </div>
                                <div class="grand-total-value">₹ <span id="grand_total_display">0</span></div>
                                <input type="hidden" id="grand_total" name="grand_total" value="{{ old('grand_total', 0) }}">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group m-0">
                                        <label class="form-label">Transaction ID</label>
                                        <input type="text" class="form-control" name="transaction_id"
                                               value="{{ old('transaction_id') }}" placeholder="TXN0000000000">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-0">
                                        <label class="form-label">
                                            <i class="mdi mdi-qrcode mr-1"></i> Event QR Code
                                        </label>
                                        @if(isset($qr_image_url) && $qr_image_url)
                                            {{-- QR code auto-fetched from the selected Event --}}
                                            <div class="mt-1 p-2 border rounded text-center" style="background:#f8f9fa;max-width:150px;">
                                                <img src="{{ $qr_image_url }}"
                                                     alt="Event QR Code"
                                                     class="img-fluid"
                                                     style="max-height:120px;max-width:120px;">
                                                <div class="small text-muted mt-1">
                                                    <i class="mdi mdi-check-circle text-success"></i> Event QR Code
                                                </div>
                                            </div>
                                            <input type="hidden" name="qr_code_existing" value="{{ $qr_image_url }}">
                                        @else
                                            <div class="mt-1 p-3 border rounded text-center text-muted" style="background:#f8f9fa;max-width:150px;">
                                                <i class="mdi mdi-qrcode-scan" style="font-size:2rem;opacity:.3;display:block;"></i>
                                                <small>No QR Code<br>
                                                @if(isset($event) && $event)
                                                    — not set on this event
                                                @else
                                                    — select an event
                                                @endif
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-0">
                                        <label class="form-label">Payment Screenshot</label>
                                        <input type="file" class="form-control-file" name="screenshot_payment" accept="image/*">
                                        <img id="screenshot_preview" class="img-fluid mt-1" src="#" alt="Screenshot" style="display:none;max-height:80px;">
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-2 mb-3">

                            <button type="submit" class="btn btn-save waves-effect waves-light">
                                <i class="mdi mdi-check-circle mr-1"></i> Submit Registration
                            </button>
                            <a href="{{ url('registrations') }}" class="btn btn-outline-secondary ml-2 waves-effect">
                                Cancel
                            </a>

                        </div>
                    </div>

                </form>
            </div>{{-- /card-body --}}
        </div>{{-- /card --}}
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     JavaScript — Dynamic rows + total calculation
════════════════════════════════════════════════════ --}}
<script>
(function () {
    // ── Blade-injected PHP data ──────────────────────────────────────
    var foodOptions = @json($foods ?? []);
    var relationOptions = @json($relations ?? []);

    // ── Helpers ─────────────────────────────────────────────────────
    function buildFoodOptions(selectedId) {
        var html = '<option value="">Select Food</option>';
        foodOptions.forEach(function (f) {
            var sel = (selectedId && String(f.id) === String(selectedId)) ? 'selected' : '';
            html += '<option value="' + f.id + '" data-amount="' + (f.amount || 0) + '" ' + sel + '>' + f.name + '</option>';
        });
        return html;
    }

    function buildRelationOptions(selectedId) {
        var html = '<option value="">— Select Relation —</option>';
        relationOptions.forEach(function (r) {
            var sel = (selectedId && String(r.id) === String(selectedId)) ? 'selected' : '';
            html += '<option value="' + r.id + '" ' + sel + '>' + r.name + '</option>';
        });
        return html;
    }

    // ── Amount Rules ────────────────────────────────────────────────
    // Member  : relation_id == 1  → ₹0,   else → ₹600
    // Award   : award_type == 'certificate' → ₹400,  else → ₹600

    function calcMemberAmount(relationSelect) {
        var val = relationSelect.value;
        if (val === '') return 0;
        return (val === '1') ? 0 : 600;
    }

    function calcAwardAmount(awardTypeSelect) {
        var val = awardTypeSelect.value;
        if (val === '') return 0;
        return (val === 'certificate') ? 400 : 600;
    }

    function recalcTotal() {
        var total = 0;
        document.querySelectorAll('.amount-field').forEach(function (el) {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('grand_total_display').textContent = total.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('grand_total').value = total.toFixed(2);
    }

    // ════════════════════════════════════════════════════════════════
    //  SECTION 2 — MEMBERS
    // ════════════════════════════════════════════════════════════════
    var memberIndex = 0;
    var membersContainer = document.getElementById('members_container');
    var memberBadge = document.getElementById('member_count_badge');

    function updateMemberBadge() {
        var count = membersContainer.querySelectorAll('.member-block').length;
        memberBadge.textContent = count + (count === 1 ? ' Member' : ' Members');
    }

    function reindexMembers() {
        membersContainer.querySelectorAll('.member-block').forEach(function (block, i) {
            block.dataset.index = i;
            block.querySelector('.block-label').innerHTML = '<i class="mdi mdi-account mr-1"></i> Member #' + (i + 1);
            block.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/\[\d+\]/, '[' + i + ']');
            });
        });
        updateMemberBadge();
    }

    function buildMemberBlock(idx) {
        var div = document.createElement('div');
        div.className = 'member-block';
        div.dataset.index = idx;
        div.innerHTML = `
            <div class="block-header">
                <span class="block-label"><i class="mdi mdi-account mr-1"></i> Member #` + (idx + 1) + `</span>
                <button type="button" class="btn-remove-row remove-member-btn" title="Remove Member">
                    <i class="mdi mdi-trash-can-outline mr-1"></i> Remove
                </button>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Surname</label>
                        <input type="text" class="form-control" name="member_surname[` + idx + `]" placeholder="Surname">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="member_name[` + idx + `]" placeholder="Member's name">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" class="form-control" name="member_mobile[` + idx + `]" placeholder="Mobile">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Relation</label>
                        <select name="relation_id[` + idx + `]" class="form-control relation-field">
                            ` + buildRelationOptions() + `
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control dob-field" name="dob[` + idx + `]">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group m-0">
                        <label class="form-label">Age</label>
                        <input type="number" class="form-control age-field" name="age[` + idx + `]" placeholder="—" min="0" max="120" readonly>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group m-0">
                        <label class="form-label">Food</label>
                        <select name="food_id[` + idx + `]" class="form-control food-field">
                            ` + buildFoodOptions() + `
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mt-2">
                    <div class="form-group m-0">
                        <label class="form-label">Amount (₹)</label>
                        <input type="text" readonly class="form-control amount-field member-amount-field" name="amount[` + idx + `]" placeholder="0">
                    </div>
                </div>
            </div>`;

        // DOB → Age auto-calc
        var dobField = div.querySelector('.dob-field');
        var ageField = div.querySelector('.age-field');
        dobField.addEventListener('change', function () {
            if (this.value) {
                var today = new Date();
                var birth = new Date(this.value);
                var age = today.getFullYear() - birth.getFullYear();
                var m = today.getMonth() - birth.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
                ageField.value = age >= 0 ? age : '';
            } else {
                ageField.value = '';
            }
        });

        // Relation → Amount  (relation_id=1 → ₹0, else → ₹600)
        div.querySelector('.relation-field').addEventListener('change', function () {
            div.querySelector('.member-amount-field').value = calcMemberAmount(this);
            recalcTotal();
        });

        // Remove
        div.querySelector('.remove-member-btn').addEventListener('click', function () {
            if (membersContainer.querySelectorAll('.member-block').length > 1) {
                div.remove();
                reindexMembers();
                recalcTotal();
            } else {
                alert('At least one member is required.');
            }
        });

        return div;
    }

    // Add initial member
    membersContainer.appendChild(buildMemberBlock(memberIndex++));

    document.getElementById('add_member_btn').addEventListener('click', function () {
        membersContainer.appendChild(buildMemberBlock(memberIndex++));
        updateMemberBadge();
    });

    // ════════════════════════════════════════════════════════════════
    //  SECTION 3 — AWARDS
    // ════════════════════════════════════════════════════════════════
    var awardIndex = 0;
    var awardsContainer = document.getElementById('awards_container');
    var awardBadge = document.getElementById('award_count_badge');

    function updateAwardBadge() {
        var count = awardsContainer.querySelectorAll('.award-block').length;
        awardBadge.textContent = count + (count === 1 ? ' Award' : ' Awards');
    }

    function reindexAwards() {
        awardsContainer.querySelectorAll('.award-block').forEach(function (block, i) {
            block.dataset.index = i;
            block.querySelector('.block-label').innerHTML = '<i class="mdi mdi-trophy mr-1"></i> Award #' + (i + 1);
            block.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/\[\d+\]/, '[' + i + ']');
            });
        });
        updateAwardBadge();
    }

    function buildAwardBlock(idx) {
        var div = document.createElement('div');
        div.className = 'award-block';
        div.dataset.index = idx;
        div.innerHTML = `
            <div class="block-header">
                <span class="block-label"><i class="mdi mdi-trophy mr-1"></i> Award #` + (idx + 1) + `</span>
                <button type="button" class="btn-remove-row remove-award-btn" title="Remove Award">
                    <i class="mdi mdi-trash-can-outline mr-1"></i> Remove
                </button>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Member's Surname</label>
                        <input type="text" class="form-control" name="award_member_surname[` + idx + `]" placeholder="Surname">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Member's Name</label>
                        <input type="text" class="form-control" name="award_member_name[` + idx + `]" placeholder="Member's name">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Gender</label>
                        <select name="award_gender[` + idx + `]" class="form-control">
                            <option value="">— Select —</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" name="award_department[` + idx + `]" placeholder="Department">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Award Category</label>
                        <input type="text" class="form-control" name="award_category[` + idx + `]" placeholder="Award Category">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group m-0">
                        <label class="form-label">Award Type</label>
                        <select name="award_type[` + idx + `]" class="form-control award-type-field">
                            <option value="">— Select —</option>
                            <option value="certificate">Certificate</option>
                            <option value="award">Award</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mt-2">
                    <div class="form-group m-0">
                        <label class="form-label">Food</label>
                        <select name="award_food_id[` + idx + `]" class="form-control award-food-field">
                            ` + buildFoodOptions() + `
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mt-2">
                    <div class="form-group m-0">
                        <label class="form-label">Amount (₹)
                        </label>
                        <input type="text" readonly class="form-control amount-field award-amount-field" name="award_amount[` + idx + `]" placeholder="0">
                    </div>
                </div>
                <div class="col-md-2 mt-2">
                    <div class="form-group m-0">
                        <label class="form-label">Photo</label>
                        <input type="file" class="form-control-file" name="award_photo[` + idx + `]" accept="image/*">
                    </div>
                </div>
            </div>
            <hr class="my-2">
            <div class="form-group m-0">
                <label class="form-label">Special Comment</label>
                <textarea class="form-control" name="award_special_comment[` + idx + `]" rows="2"
                          placeholder="Recognition note, achievements, etc."></textarea>
            </div>`;

        // Award Type → Amount  (certificate=₹400, else ₹600)
        div.querySelector('.award-type-field').addEventListener('change', function () {
            div.querySelector('.award-amount-field').value = calcAwardAmount(this);
            recalcTotal();
        });

        // Remove
        div.querySelector('.remove-award-btn').addEventListener('click', function () {
            if (awardsContainer.querySelectorAll('.award-block').length > 1) {
                div.remove();
                reindexAwards();
                recalcTotal();
            } else {
                alert('At least one award entry is required.');
            }
        });

        return div;
    }

    // Add initial award
    awardsContainer.appendChild(buildAwardBlock(awardIndex++));

    document.getElementById('add_award_btn').addEventListener('click', function () {
        awardsContainer.appendChild(buildAwardBlock(awardIndex++));
        updateAwardBadge();
    });

    // ════════════════════════════════════════════════════════════════
    //  Image Previews
    // ════════════════════════════════════════════════════════════════
    function previewImage(inputId, previewId) {
        var input = document.getElementById(inputId);
        var preview = document.getElementById(previewId);
        if (!input || !preview) return;
        input.addEventListener('change', function () {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
    previewImage('company_logo', 'company_logo_preview');

    var ssInput = document.querySelector('[name="screenshot_payment"]');
    var ssPreview = document.getElementById('screenshot_preview');
    if (ssInput && ssPreview) {
        ssInput.addEventListener('change', function () {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    ssPreview.src = e.target.result;
                    ssPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Tooltips
    if (typeof $ !== 'undefined') { $('[data-toggle="tooltip"]').tooltip(); }
})();
</script>

@endsection