@extends('admin.layouts.app')
@section('title', 'Manage Users')
@section('content')
<style>
    .select2-container{
    width: 100% !important;
    }
</style>
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-bold mt-2">Users</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header">
                    <a href="#" data-toggle="modal" data-target="#user-create" class="btn btn-primary waves-effect waves-light btn-sm float-right">Add New User</a>
            </div>
            <div class="card-body">
                <table id="UsersTable" class="table dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Sr No.</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="user-create" tabindex="-1" role="dialog" aria-labelledby="userCreateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userCreateLabel">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>First Name</label>
                            <input name="first_name" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Last Name</label>
                            <input name="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Mobile</label>
                            <input name="mobile" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input name="email" type="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input name="password" type="password" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Confirm Password</label>
                            <input name="password_confirmation" type="password" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection