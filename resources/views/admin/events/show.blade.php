@extends('admin.layouts.app')
@section('title', 'Event Details')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4>{{ $event->name }}</h4>
        @if($event->image)
          <img src="{{ asset('storage/' . $event->image) }}" style="max-width:200px;" />
        @endif
        <p>{{ $event->description }}</p>
        <p><strong>Used:</strong> {{ $event->used ? 'Yes' : 'No' }}</p>
        <a href="{{ route('events.index') }}" class="btn btn-secondary">Back</a>
        <a href="{{ route('events.edit', $event) }}" class="btn btn-primary">Edit</a>
      </div>
    </div>
  </div>
</div>
@endsection
