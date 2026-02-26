@extends('admin.layouts.app')
@section('title','Create User')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
          @csrf
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
          <button class="btn btn-primary" type="submit">Create</button>
          <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
