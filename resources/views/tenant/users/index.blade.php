@extends('tenant.layouts.app')
@section('title', 'Manage User')
@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Manage User</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">User</li>
                </ol>
            </div>
            
        </div>
    </div>
</div>     
<!-- end page title -->

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Users
          @can('user-create')
            <a href="{{ route('users.create') }}" class="btn btn-success waves-effect waves-light btn-sm float-right">Add New User</a>
          @endcan
        </h3>
      </div>
      <div class="card-body">
        <table id="userTable" class="table dt-responsive nowrap">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th> 
              <th>Email</th> 
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            <tr>
              <th>#</th>
              <th>Name</th> 
              <th>Email</th>
              <th>Actions</th>
            </tr>
            </tfoot>
        </table>              
      </div>
    </div>
  </div>
</div>

{{-- <!-- Toast Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to proceed?</p>
                <textarea class="form-control" id="comment" placeholder="Add a comment (optional)"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div> --}}

@endsection
