@extends('admin.layouts.app')
@section('title','User Details')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
        <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
      </div>
    </div>
  </div>
</div>
@endsection
