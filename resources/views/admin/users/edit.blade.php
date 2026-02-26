@extends('admin.layouts.app')
@section('title','Edit User')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>First Name</label>
              <input name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
            </div>
            <div class="form-group col-md-6">
              <label>Last Name</label>
              <input name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Mobile</label>
              <input name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
            </div>
            <div class="form-group col-md-6">
              <label>Email</label>
              <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Password (leave blank to keep current)</label>
              <input name="password" type="password" class="form-control">
            </div>
            <div class="form-group col-md-6">
              <label>Confirm Password</label>
              <input name="password_confirmation" type="password" class="form-control">
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Update</button>
          <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
