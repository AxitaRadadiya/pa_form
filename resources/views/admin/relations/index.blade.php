@extends('admin.layouts.app')
@section('content')

<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-flex align-items-center justify-content-between">
      <h3 class="mb-0 font-bold mt-2">Relations</h3>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="RelationTable" class="table dt-responsive nowrap table-sm">
            <thead>
              <tr>
                <th>Sr No.</th>
                <th>Name</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
