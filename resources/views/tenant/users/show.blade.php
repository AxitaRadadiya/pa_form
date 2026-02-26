@extends('tenant.layouts.app')
@section('title', 'User Information')
@section('content')

<div class="row">
  <div class="col-12">
      <div class="page-title-box d-flex align-items-center justify-content-between">
          <h4 class="mb-0 font-size-18">User</h4>

          <div class="page-title-right">
              <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="{{route('users.index')}}">User</a></li>
                  <li class="breadcrumb-item active">Show</li>
              </ol>
          </div>
          
      </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">User</h4>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group row">
                <label for="inputEmail1" class="col-md-4 col-form-label">Name</label>
                <div class="col-md-8 pt-2">
                    {{ $user->name }}
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group row">
                <label for="inputEmail2" class="col-md-4 col-form-label">Email</label>
                <div class="col-md-8 pt-2">
                  {{ $user->email }}
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

@endsection
