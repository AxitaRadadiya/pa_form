@extends('admin.layouts.app')
@section('title', 'Edit Event')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="form-group">
            <label>Event Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $event->name) }}" required>
          </div>

          <div class="form-group">
            <label>Image</label>
            @if($event->image)
              <div><img src="{{ asset('storage/' . $event->image) }}" style="max-width:120px;" /></div>
            @endif
            <input type="file" name="image" class="form-control">
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $event->description) }}</textarea>
          </div>

          <div class="form-group form-check">
            <input type="checkbox" name="used" id="used" class="form-check-input" value="1" {{ $event->used ? 'checked' : '' }}>
            <label for="used" class="form-check-label">Used</label>
          </div>

          <button class="btn btn-primary" type="submit">Update</button>
          <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
