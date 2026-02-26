@extends('admin.layouts.app')
@section('title', 'Manage Activity')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Manage Activity</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity</li>
                </ol>
            </div>
            
        </div>
    </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Activity
        </h3>
      </div>
      <div class="card-body">
        <table id="ActivityTable" class="table dt-responsive nowrap">
          <thead>
            <tr>
              
              <th>Description</th>
              <th>Causer</th>
              <th>Subject</th>
              <th>Log Name</th>
              <th>Time</th>
   
            </tr>
            </thead>
             <tbody>
            </tbody>
            <tfoot>
            <tr>
                
              <th>Description</th>
              <th>Causer</th>
              <th>Subject</th>
              <th>Log Name</th>
              <th>Time</th>
   
            </tr>
            </tfoot>
        </table>              
      </div>
    </div>
  </div>
</div>     

@endsection
