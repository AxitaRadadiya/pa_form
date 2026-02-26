@extends('admin.layouts.app')
@section('content')

<!-- start page title -->
    <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 font-size-18">Dashboard</h4>

              <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item active">Dashboard</li>
                  </ol>
              </div> 
          </div>
      </div>
    </div>     
    <!-- end page title -->
    <div class="row">
        <div class="col-md-4 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="mb-2">
                        <h5 class="card-title mb-0">Total Members Registered</h5>
                    </div>
                    <div class="row d-flex align-items-center mb-2">
                        <div class="col-4">
                            <h2 class="d-flex align-items-center mb-0">
                                {{ $totalMembers ?? 0 }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col-->
    </div>

@endsection
