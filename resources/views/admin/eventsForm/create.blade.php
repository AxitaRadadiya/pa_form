@extends('admin.layouts.app')
@section('title', 'Register Member')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════
     Inline styles — only tiny overrides that Bootstrap/theme don't cover
═══════════════════════════════════════════════════════════════════════ --}}
<style>
    /* Section header stripe */
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
        width: 26px;
        height: 26px;
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
    .section-badge {
        margin-left: auto;
    }

    /* Member block */
    .member-block {
        border: 1px solid #dee2e6;
        border-left: 3px solid #251c4b;
        border-radius: .25rem;
        padding: 1rem;
        margin-bottom: .85rem;
        background: #fdfdff;
        transition: box-shadow .15s;
    }
    .member-block:hover {
        box-shadow: 0 2px 8px rgba(37,28,75,.1);
    }
    .member-block-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .75rem;
        padding-bottom: .6rem;
        border-bottom: 1px solid #eee;
    }
    .member-label {
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #251c4b;
    }

    /* Add member button */
    /* #add_member {
        width: 100%;
        border: 2px dashed #b8b4cc;
        background: transparent;
        color: #251c4b;
        border-radius: .25rem;
        padding: .6rem;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        margin-top: .25rem;
        font-family: "Roboto", sans-serif;
    }
    #add_member:hover {
        background: #251c4b;
        border-color: #251c4b;
        color: #fff;
    } */

    /* Grand Total box */
    .grand-total-box {
        background: #251c4b;
        border-radius: .35rem;
        padding: .9rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        color: #fff;
    }
    .grand-total-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        opacity: .75;
        font-weight: 600;
    }
    .grand-total-value {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: .02em;
    }

    /* Preview images */
    .img-thumb {
        display: none;
        max-height: 100px;
        margin-top: .5rem;
        border-radius: .25rem;
        border: 1px solid #dee2e6;
    }

    /* form-label bold */
    .form-label {
        font-weight: 500;
        font-size: .82rem;
        color: #495057;
        margin-bottom: .3rem;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8 d-flex">
        <div class="card w-100">

            {{-- ── Card header ── --}}
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

                {{-- ── Event Banner ── --}}
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

                    {{-- ══════════════════════════════════════════════
                         SECTION 1 — Personal / Company
                    ══════════════════════════════════════════════ --}}
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
                                        <label class="form-label" for="first_name">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="first_name"
                                               name="first_name"
                                               value="{{ old('first_name', auth()->user()->first_name ?? '') }}"
                                               placeholder="First name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="last_name">Last Name</label>
                                        <input type="text" class="form-control" id="last_name"
                                               name="last_name"
                                               value="{{ old('last_name', auth()->user()->last_name ?? '') }}"
                                               placeholder="Last name">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="mobile">Mobile Number</label>
                                        <input type="tel" class="form-control" id="mobile"
                                               name="mobile"
                                               value="{{ old('mobile', auth()->user()->mobile ?? '') }}"
                                               placeholder="+91 00000 00000">
                                    </div>
                                </div>
								<div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="email">Email Address</label>
                                        <input type="email" class="form-control" id="email"
                                               name="email"
                                               value="{{ old('email', auth()->user()->email ?? '') }}"
                                               placeholder="you@example.com">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="chapter_id">
                                            Chapter Name <span class="text-danger">*</span>
                                        </label>
                                        <select id="chapter_id" name="chapter_id" class="form-control" required>
                                            <option value="">— Select Chapter —</option>
                                            @if(isset($chapters))
                                                @foreach($chapters as $c)
                                                    <option value="{{ $c->id }}"
                                                        {{ old('chapter_id') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
								<div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="chain_name">GA-Chain Name<span class="text-danger">*</span></label>
                                             <input type="text" class="form-control" id="chain_name"
                                                 name="chain_name" value="{{ old('chain_name') }}"
                                                 placeholder="Chain name" required>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="company_name">Company Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="company_name"
                                               name="company_name" value="{{ old('company_name') }}"
                                               placeholder="ABC Pvt. Ltd." required>
                                    </div>
                                </div>
                                <div class="col-md-4">
								 <div class="form-group m-0">
                                	<label class="form-label" for="about_company">About Company</label>
                                	<textarea class="form-control" id="about_company"
                                          name="about_company" rows="2"
                                          placeholder="Brief description of the company…">{{ old('about_company') }}</textarea>
                            	</div>
								</div>
                                <div class="col-md-3">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="company_logo">Company Logo<span class="text-danger">*</span></label>
                                        <input type="file" class="form-control-file" id="company_logo"
                                               name="company_logo" accept="image/*" required>
                                        <img id="company_logo_preview" class="img-thumb img-fluid" src="#" alt="Logo">
                                    </div>
                                </div>
								
                            </div>

                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════
                         SECTION 2 — Member Details (Dynamic)
                    ══════════════════════════════════════════════ --}}
                    <div class="card mb-2 shadow-sm">
                        <div class="section-header">
                            <span class="section-number">2</span>
                            <h6 class="section-title">Member Details</h6>
                            <span class="section-badge">
                                <span class="badge badge-secondary badge-pill" id="member_count_badge">1 Member</span>
                            </span>
                        </div>
                        <div class="card-body">

                            <div class="form-group m-0">
                                <label class="form-label" for="section_description">Section Description</label>
                                <textarea class="form-control" id="section_description"
                                          name="section_description" rows="2"
                                          placeholder="Optional notes for this section…">{{ old('section_description') }}</textarea>
                            </div>

                            {{-- Dynamic member rows --}}
                            @php $memberCount = max(1, count(old('member_name', []))); @endphp

                            <div id="members_container">
                                @for ($i = 0; $i < $memberCount; $i++)
                                <div class="member-block" data-index="{{ $i }}">
                                    <div class="member-block-header">
                                        <span class="member-label">
                                            <i class="mdi mdi-account mr-1"></i> Member #{{ $i + 1 }}
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-member">
                                            <i class="mdi mdi-close"></i> Remove
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group m-0">
                                                <label class="form-label">Surname</label>
                                                <input type="text" class="form-control" name="member_surname[{{ $i }}]"
                                                       value="{{ old('member_surname.'.$i) }}" placeholder="Surname">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group m-0">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="member_name[{{ $i }}]"
                                                       value="{{ old('member_name.'.$i) }}" placeholder="Full name">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group m-0">
                                                <label class="form-label">Mobile Number</label>
                                                <input type="tel" class="form-control" name="member_mobile[{{ $i }}]"
                                                       value="{{ old('member_mobile.'.$i) }}" placeholder="Mobile">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group m-0">
                                                <label class="form-label">Relation</label>
                                                <select name="relation_id[{{ $i }}]" class="form-control">
                                                    <option value="">— Select Relation —</option>
                                                    @if(isset($relations))
                                                        @foreach($relations as $r)
                                                            <option value="{{ $r->id }}"
                                                                {{ old('relation_id.'.$i) == $r->id ? 'selected' : '' }}>
                                                                {{ $r->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group m-0">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="date" class="form-control dob-field"
                                                       name="dob[{{ $i }}]" value="{{ old('dob.'.$i) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group m-0">
                                                <label class="form-label">Age</label>
                                                <input type="number" class="form-control age-field"
                                                       name="age[{{ $i }}]" value="{{ old('age.'.$i) }}" placeholder="—" min="0" max="120">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group m-0">
                                                <label class="form-label">Food</label>
                                                <select name="food_id[{{ $i }}]" class="form-control food-field">
                                                    <option value="">Select Food</option>
                                                    @if(isset($foods))
                                                        @foreach($foods as $f)
                                                            <option value="{{ $f->id }}"
                                                                data-amount="{{ $f->amount ?? 0 }}"
                                                                {{ old('food_id.'.$i) == $f->id ? 'selected' : '' }}>
                                                                {{ $f->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group m-0">
                                                <label class="form-label">Amount (₹)</label>
                                                <input type="text" readonly class="form-control amount-field"
                                                       name="amount[{{ $i }}]" value="{{ old('amount.'.$i) }}" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>

                            {{-- Add Member Button --}}
                            <button type="button" id="add_member">
                                <i class="mdi mdi-plus-circle mr-1"></i> Add Another Member
                            </button>

                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════
                         SECTION 3 — Awards & Member Details
                    ══════════════════════════════════════════════ --}}
                    <div class="card mb-2 shadow-sm">
                        <div class="section-header">
                            <span class="section-number">3</span>
                            <h6 class="section-title">Awards &amp; Member Details</h6>
                            <span class="section-badge">
                                <span class="badge badge-warning badge-pill">Certificates</span>
                            </span>
                        </div>
                        <div class="card-body">

                            <div class="form-group mb-2">
                                <label class="form-label" for="section3_description">Section Description</label>
                                <textarea class="form-control" id="section3_description"
                                          name="section3_description" rows="2"
                                          placeholder="Optional notes…">{{ old('section3_description') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="member_first_name">Member's Surname</label>
                                        <input type="text" class="form-control" id="member_first_name"
                                               name="member_first_name" value="{{ old('member_first_name') }}"
                                               placeholder="First name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="member_last_name">Member's Name</label>
                                        <input type="text" class="form-control" id="member_last_name"
                                               name="member_last_name" value="{{ old('member_last_name') }}"
                                               placeholder="Last name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="relation_id_section3">Gender</label>
                                         <select id="gender" name="gender" class="form-control">
                                            <option value="">— Select Option —</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="relation_id_section3">Department</label>
                                         <input type="text" class="form-control" id="department"
                                               name="award_department" value="{{ old('award_department') }}"
                                               placeholder="Award Department">
                                    </div>
                                    </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="award_category">Award Category</label>
                                       <input type="text" class="form-control" id="award_category"
                                               name="award_category" value="{{ old('award_category') }}"
                                               placeholder="Award Category">
                                    </div>
                                </div>
								<div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="award_type">Award Type</label>
                                        <select id="award_type" name="award_type" class="form-control">
                                            <option value="">— Select Option —</option>
                                            <option value="certificate" {{ old('award_type') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                                            <option value="award" {{ old('award_type') == 'award' ? 'selected' : '' }}>Award</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="food_id_section3">Food </label>
                                        <select id="food_id_section3" name="food_id_section3" class="form-control">
                                            <option value="">— Select Food —</option>
                                            @if(isset($foods))
                                                @foreach($foods as $f)
                                                    <option value="{{ $f->id }}"
                                                        data-amount="{{ $f->amount ?? 0 }}"
                                                        {{ old('food_id_section3') == $f->id ? 'selected' : '' }}>
                                                        {{ $f->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2 mt-2">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="amount_section3">Amount (₹)</label>
                                        <input type="text" readonly class="form-control"
                                               id="amount_section3" name="amount_section3"
                                               value="{{ old('amount_section3') }}" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-2 mt-2">
                                    <div class="form-group m-0 mb-0">
                                        <label class="form-label" for="photo_attached">Photo Attached
                                            <i class="mdi mdi-information-outline ml-1 text-muted" data-toggle="tooltip" title="Attach member photo (optional). Max 2MB."></i>
                                        </label>
                                        <input type="file" class="form-control-file" id="photo_attached"
                                               name="photo_attached" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- Repeatable awards container -->
                            <div id="awards_container" class="mb-3">
                                <!-- Existing (primary) award fields stay as-is and submit as single set -->
                            </div>

                            <div class="text-right mb-3">
                                <button type="button" id="add_award" class="btn btn-sm btn-outline-primary">+ Add Award</button>
                            </div>

                            <hr class="my-3">
                            <div class="form-group m-0">
                                <label class="form-label" for="special_comment">Special Comment for Member</label>
                                <textarea class="form-control" id="special_comment"
                                          name="special_comment" rows="2"
                                          placeholder="Recognition note, achievements, etc.">{{ old('special_comment') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════
                         SECTION 4 — Images & Payment
                    ══════════════════════════════════════════════ --}}
                    <div class="card mb-2 shadow-sm">
                        <div class="card-body">
                            {{-- Grand Total --}}
                            <div class="grand-total-box">
                                <div>
                                    <div class="grand-total-label">Grand Total</div>
                                    <small style="opacity:.6;">Auto-calculated from all members</small>
                                </div>
                                <div class="grand-total-value">
                                    ₹ <span id="grand_total_display">0</span>
                                </div>
                                <input type="hidden" id="grand_total" name="grand_total" value="{{ old('grand_total', 0) }}">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="transaction_id">Transaction ID</label>
                                        <input type="text" class="form-control" id="transaction_id"
                                               name="transaction_id" value="{{ old('transaction_id') }}"
                                               placeholder="TXN0000000000">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="qr_code">QR Code (image)</label>
                                        <input type="file" class="form-control-file" id="qr_code"
                                               name="qr_code" accept="image/*">
                                        <img id="qr_preview" class="img-thumb img-fluid" src="#" alt="QR">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group m-0">
                                        <label class="form-label" for="screenshot_payment">Payment Screenshot</label>
                                        <input type="file" class="form-control-file" id="screenshot_payment"
                                               name="screenshot_payment" accept="image/*">
                                        <img id="screenshot_preview" class="img-thumb img-fluid" src="#" alt="Screenshot">
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

{{-- ══ Hidden award template — outside form so it is never submitted ══ --}}
<div id="award_template" style="display:none;" aria-hidden="true">
    <div class="award-block mb-3" data-idx="__IDX__">
        <div class="d-flex align-items-center mb-2">
            <strong class="mr-2">Award #<span class="award-num">__NUM__</span></strong>
            <button type="button" class="btn btn-sm btn-outline-danger ml-auto remove-award">-</button>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label class="form-label">Award Name</label>
                <input name="award_name_extra[]" class="form-control">
            </div>
            <div class="form-group col-md-2">
                <label class="form-label">Type</label>
                <select name="award_type_extra[]" class="form-control award-type-extra">
                    <option value="">Select</option>
                    <option value="certificate">Certificate</option>
                    <option value="award">Award</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label class="form-label">Amount</label>
                <input name="amount_section3_extra[]" class="form-control amount-section3-extra" readonly value="0">
            </div>
            <div class="form-group col-md-4">
                <label class="form-label">Food</label>
                <select name="food_id_section3_extra[]" class="form-control food-section3-extra">
                    <option value="">— Select Food —</option>
                    @if(isset($foods))
                        @foreach($foods as $f)
                            <option value="{{ $f->id }}" data-amount="{{ $f->amount ?? 0 }}">{{ $f->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>

{{-- ══ Hidden member template — rendered by Blade, cloned by JS ══ --}}
<div id="member_template" style="display:none;" aria-hidden="true">
    <div class="member-block" data-index="__IDX__">
        <div class="member-block-header">
            <span class="member-label"><i class="mdi mdi-account mr-1"></i> Member #<span class="member-num">1</span></span>
            <button type="button" class="btn btn-sm btn-outline-danger remove-member">
                <i class="mdi mdi-close"></i> Remove
            </button>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group m-0">
                    <label class="form-label">Surname</label>
                    <input type="text" class="form-control" name="member_surname[__IDX__]" placeholder="Surname">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group m-0">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="member_name[__IDX__]" placeholder="Full name">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group m-0">
                    <label class="form-label">Mobile Number</label>
                    <input type="tel" class="form-control" name="member_mobile[__IDX__]" placeholder="Mobile">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group m-0">
                    <label class="form-label">Relation</label>
                    <select name="relation_id[__IDX__]" class="form-control">
                        <option value="">— Select Relation —</option>
                        @if(isset($relations))
                            @foreach($relations as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group m-0">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control dob-field" name="dob[__IDX__]">
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group m-0">
                    <label class="form-label">Age</label>
                    <input type="number" class="form-control age-field" name="age[__IDX__]" placeholder="—" min="0" max="120">
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group m-0">
                    <label class="form-label">Food</label>
                    <select name="food_id[__IDX__]" class="form-control food-field">
                        <option value="">Select Food</option>
                        @if(isset($foods))
                            @foreach($foods as $f)
                                <option value="{{ $f->id }}" data-amount="{{ $f->amount ?? 0 }}">{{ $f->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group m-0">
                    <label class="form-label">Amount (₹)</label>
                    <input type="text" readonly class="form-control amount-field" name="amount[__IDX__]" placeholder="0">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    /* ══ AWARD TYPE AMOUNTS — align with server calcAwardAmount() ═════ */
    var AWARD_AMOUNTS = { certificate: 500, award: 1000 };

    /* ── 1. Image preview ─────────────────────────────────────────── */
    function bindPreview(inputId, previewId) {
        var el = document.getElementById(inputId);
        if (!el) return;
        el.addEventListener('change', function () {
            var img = document.getElementById(previewId);
            if (!img) return;
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) { img.src = e.target.result; img.style.display = 'block'; };
                reader.readAsDataURL(this.files[0]);
            } else {
                img.src = '#'; img.style.display = 'none';
            }
        });
    }
    bindPreview('company_logo',       'company_logo_preview');
    bindPreview('qr_code',            'qr_preview');
    bindPreview('screenshot_payment', 'screenshot_preview');

    /* ── 2. Calculate age from DOB ────────────────────────────────── */
    function calcAge(dobValue) {
        if (!dobValue) return null;
        var d = new Date(dobValue);
        if (isNaN(d.getTime())) return null;
        var today = new Date();
        var age = today.getFullYear() - d.getFullYear();
        var m   = today.getMonth() - d.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < d.getDate())) age--;
        return age >= 0 ? age : 0;
    }

    /* ── 3. Amount from age ───────────────────────────────────────── */
    function amountFromAge(age) {
        if (age === null || age === undefined || age === '') return 0;
        // Match server: age > 5 => 1000, else => 500
        return (parseInt(age, 10) > 5) ? 1000 : 500;
    }

    /* ── 4. Grand total ───────────────────────────────────────────── */
    function recalcTotal() {
        var total = 0;
        document.querySelectorAll('#members_container .amount-field').forEach(function (el) {
            total += parseFloat(el.value) || 0;
        });
        var s3 = document.getElementById('amount_section3');
        if (s3) total += parseFloat(s3.value) || 0;
        // Add all extra award amounts
        document.querySelectorAll('.amount-section3-extra').forEach(function (el) {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('grand_total').value = total;
        var disp = document.getElementById('grand_total_display');
        if (disp) disp.textContent = total.toLocaleString('en-IN');
    }

    /* ── 5. Member count badge ────────────────────────────────────── */
    function updateBadge() {
        var count = document.querySelectorAll('#members_container .member-block').length;
        var badge = document.getElementById('member_count_badge');
        if (badge) badge.textContent = count + (count === 1 ? ' Member' : ' Members');
    }

    /* ── 6. Renumber labels + re-index names after remove ─────────── */
    function renumber() {
        document.querySelectorAll('#members_container .member-block').forEach(function (block, i) {
            var lbl = block.querySelector('.member-label');
            if (lbl) lbl.innerHTML = '<i class="mdi mdi-account mr-1"></i> Member #' + (i + 1);
            block.setAttribute('data-index', i);
            block.querySelectorAll('[name]').forEach(function (el) {
                // Replace both numeric indexes [0], [1]... and template placeholder [__IDX__]
                el.name = el.name.replace(/\[(__IDX__|\d+)\]/, '[' + i + ']');
            });
        });
        updateBadge();
    }

    /* ── 7. Update single member amount ──────────────────────────── */
    function updateMemberAmount(block) {
        if (!block) return;
        var ageField = block.querySelector('.age-field');
        var amtField = block.querySelector('.amount-field');
        if (!amtField) return;
        // If a food is selected and provides an amount, prefer that.
        var foodSel = block.querySelector('.food-field');
        var foodAmount = null;
        if (foodSel && foodSel.selectedOptions && foodSel.selectedOptions[0]) {
            var parsedFood = parseFloat(foodSel.selectedOptions[0].dataset.amount);
            if (!isNaN(parsedFood) && foodSel.selectedOptions[0].value !== '') {
                foodAmount = parsedFood;
            }
        }
        var age = (ageField && ageField.value !== '') ? parseInt(ageField.value, 10) : null;
        if (foodAmount !== null) {
            amtField.value = foodAmount;
        } else {
            amtField.value = (age !== null) ? amountFromAge(age) : 0;
        }
    }

    /* ── 8b. Input event delegation (for live age typing) ────────── */
    document.addEventListener('input', function (e) {
        var t = e.target;
        if (t.classList.contains('age-field')) {
            updateMemberAmount(t.closest('.member-block'));
            recalcTotal();
        }
    });

    /* ── 8. Change event delegation ──────────────────────────────── */
    document.addEventListener('change', function (e) {
        var t = e.target;

        if (t.classList.contains('dob-field')) {
            var block    = t.closest('.member-block');
            var ageField = block && block.querySelector('.age-field');
            var age      = calcAge(t.value);
            if (ageField) ageField.value = (age !== null) ? age : '';
            updateMemberAmount(block);
            recalcTotal();
        }

        if (t.classList.contains('food-field')) {
            // When food selection changes, update member amount to food amount (if provided)
            var block = t.closest('.member-block');
            updateMemberAmount(block);
            recalcTotal();
        }

        if (t.classList.contains('age-field')) {
            updateMemberAmount(t.closest('.member-block'));
            recalcTotal();
        }

        if (t.id === 'award_type') {
            var el3 = document.getElementById('amount_section3');
            if (el3) el3.value = AWARD_AMOUNTS[t.value] || '';
            recalcTotal();
        }

        if (t.id === 'food_id_section3') {
            var atSel = document.getElementById('award_type');
            if (!(atSel && AWARD_AMOUNTS[atSel.value])) {
                var opt3 = t.selectedOptions[0];
                var el3b = document.getElementById('amount_section3');
                if (el3b) el3b.value = opt3 ? (parseFloat(opt3.dataset.amount) || '') : '';
            }
            recalcTotal();
        }
    });

    /* ── 9. Remove button ─────────────────────────────────────────── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.remove-member');
        if (!btn) return;
        var block     = btn.closest('.member-block');
        if (!block) return;
        var container = document.getElementById('members_container');

        if (container.querySelectorAll('.member-block').length <= 1) {
            // Last block — just clear, don't remove
            block.querySelectorAll('input:not([readonly])').forEach(function (i) { i.value = ''; });
            block.querySelectorAll('select').forEach(function (s) { s.selectedIndex = 0; });
            var af = block.querySelector('.amount-field');
            if (af) af.value = '';
            recalcTotal();
            return;
        }
        block.remove();
        renumber();
        recalcTotal();
    });

    /* ── 10. Add Member — clone Blade-rendered hidden template ────── */
    document.getElementById('add_member').addEventListener('click', function () {
        var container = document.getElementById('members_container');
        var template  = document.querySelector('#member_template .member-block');
        if (!container || !template) return;

        var newIndex = container.querySelectorAll('.member-block').length;

        // Deep clone the Blade-rendered template (has correct select options)
        var newBlock = template.cloneNode(true);

        // Replace all __IDX__ placeholders in name attributes AND id attributes
        newBlock.querySelectorAll('[name]').forEach(function (el) {
            el.name = el.name.replace(/__IDX__/g, newIndex);
        });
        newBlock.querySelectorAll('[id]').forEach(function (el) {
            el.id = el.id.replace(/__IDX__/g, newIndex);
        });

        // Clear any values (template should be empty, but be safe)
        newBlock.querySelectorAll('input:not([readonly])').forEach(function (el) { el.value = ''; });
        newBlock.querySelectorAll('select').forEach(function (el) { el.selectedIndex = 0; });
        newBlock.querySelectorAll('.amount-field').forEach(function (el) { el.value = ''; });

        // Update label number
        newBlock.setAttribute('data-index', newIndex);
        var lbl = newBlock.querySelector('.member-label');
        if (lbl) lbl.innerHTML = '<i class="mdi mdi-account mr-1"></i> Member #' + (newIndex + 1);

        container.appendChild(newBlock);
        updateBadge();
        recalcTotal();

        // Scroll + focus
        newBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
        var firstInput = newBlock.querySelector('input[type="text"]');
        if (firstInput) setTimeout(function () { firstInput.focus(); }, 300);
    });

    /* ── 11. Repeatable awards (frontend only) ───────────────────── */
    function updateAwardNumbers() {
        document.querySelectorAll('#awards_container .award-block').forEach(function (blk, i) {
            var num = blk.querySelector('.award-num'); if (num) num.textContent = (i + 2); // primary is #1
            blk.setAttribute('data-idx', i);
        });
    }

    var addAwardBtn = document.getElementById('add_award');
    if (addAwardBtn) {
        addAwardBtn.addEventListener('click', function () {
            var container = document.getElementById('awards_container');
            var template  = document.getElementById('award_template');
            if (!container || !template) return;
            var newIdx = container.querySelectorAll('.award-block').length;

            // Clone template block
            var proto = template.querySelector('.award-block');
            if (!proto) return;
            var clone = proto.cloneNode(true);

            // Replace __IDX__ placeholder in name/id attributes
            clone.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/__IDX__/g, newIdx);
            });
            clone.querySelectorAll('[id]').forEach(function (el) {
                el.id = el.id.replace(/__IDX__/g, newIdx);
            });

            // Replace __NUM__ text in inner HTML
            clone.innerHTML = clone.innerHTML.replace(/__NUM__/g, newIdx + 2);

            // Clear input values and reset selects
            clone.querySelectorAll('input').forEach(function (inp) { inp.value = ''; });
            clone.querySelectorAll('select').forEach(function (s) { s.selectedIndex = 0; });

            // Set dataset index and visible number
            clone.setAttribute('data-idx', newIdx);
            var num = clone.querySelector('.award-num');
            if (num) num.textContent = (newIdx + 2);

            container.appendChild(clone);
            updateAwardNumbers();
            clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    }

    // Delegated click for removing extra award blocks
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.remove-award');
        if (!btn) return;
        var blk = btn.closest('.award-block'); if (!blk) return;
        blk.remove();
        updateAwardNumbers();
        recalcTotal();
    });

    // Delegated change handling for extra award type / food to calculate amount
    document.addEventListener('change', function (e) {
        var t = e.target;
        if (t.classList.contains('award-type-extra')) {
            var blk = t.closest('.award-block');
            var amt = blk.querySelector('.amount-section3-extra');
            if (amt) amt.value = AWARD_AMOUNTS[t.value] || '';
            recalcTotal();
        }
        if (t.classList.contains('food-section3-extra')) {
            var blk = t.closest('.award-block');
            var opt = t.selectedOptions[0];
            var amt = blk.querySelector('.amount-section3-extra');
            if (amt) amt.value = opt ? (parseFloat(opt.dataset.amount) || '') : '';
            recalcTotal();
        }
    });

    /* ── 12. Init ─────────────────────────────────────────────────── */
    (function () {
        var at  = document.getElementById('award_type');
        var el3 = document.getElementById('amount_section3');
        if (at && el3 && at.value && AWARD_AMOUNTS[at.value]) el3.value = AWARD_AMOUNTS[at.value];
    })();

    recalcTotal();
    updateBadge();

    // Initialize Bootstrap tooltips if available
    if (window.jQuery && typeof jQuery.fn.tooltip === 'function') {
        jQuery(function(){ jQuery('[data-toggle="tooltip"]').tooltip(); });
    }

})();
</script>
@endpush

@endsection