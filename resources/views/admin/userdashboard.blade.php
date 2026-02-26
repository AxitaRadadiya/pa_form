@extends('admin.layouts.app')
@section('content')

<!-- start page title -->
    <div class="row">
      <div class="col-12">
          <div class="page-title-box d-flex align-items-center justify-content-between">
              <h4 class="mb-0 font-size-18">Dashboard</h4>

              <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item active">Dashboard</li>
                  </ol>
              </div> 
          </div>
      </div>
    </div>     
    <div class="row mt-4">
        <div class="col-12">
            <h5 class="mb-3">Events</h5>
        </div>
        @if(isset($events) && $events->count())
            @foreach($events as $event)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        @php
                            $imgSrc = null;
                            if (! empty($event->image)) {
                                if (\Illuminate\Support\Str::startsWith($event->image, ['http://', 'https://'])) {
                                    $imgSrc = $event->image;
                                } else {
                                    $imgSrc = asset('storage/' . $event->image);
                                }
                            }
                        @endphp
                        @if($imgSrc)
                            <a href="{{ url('registrations/create') . '?event=' . $event->id }}">
                                <img src="{{ $imgSrc }}" class="card-img-top" alt="{{ $event->name }}">
                            </a>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="#">{{ $event->name }}</a>
                            </h5>
                            @if($event->description)
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <p>No events available.</p>
            </div>
        @endif
    </div>

@endsection
