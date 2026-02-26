@extends('tenant.layouts.app')
@section('title', 'Edit User')
@section('content')

<div class="row">
  <div class="col-12">
      <div class="page-title-box d-flex align-items-center justify-content-between">
          <h4 class="mb-0 font-size-18">User Edit Form</h4>

          <div class="page-title-right">
              <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="{{route('users.index')}}">User</a></li>
                  <li class="breadcrumb-item active">Edit</li>
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
          <form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method("PUT")
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Name</label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $user->name }}">
                  @if ($errors->has('name'))
                      <span class="text-danger">{{ $errors->first('name') }}</span>
                  @endif
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Email</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $user->email }}" readonly>
                  @if ($errors->has('email'))
                      <span class="text-danger">{{ $errors->first('email') }}</span>
                  @endif
                </div>  
              </div>
            </div>
            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
          </form>
      </div>
    </div>
  </div>
</div>

@endsection
