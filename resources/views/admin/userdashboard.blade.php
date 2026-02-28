@extends('admin.layouts.app')
@section('content')

<style>
    /* Event card banner: keep 16:9 aspect and cover crop */
    .event-card { border: 0; }
    .event-img-wrapper { position: relative; width: 100%; padding-top: 56.25%; overflow: hidden; background: #f5f5f5; }
    .event-img-wrapper img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
    .event-card .card-body { padding: .6rem; }
    .event-card .card-title { font-size: .95rem; margin-bottom: .25rem; }
    .event-card .card-text { font-size: .8rem; margin-bottom: 0; }
</style>

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
                <div class="col-md-2 mb-3">
                    <div class="card">
                        @php
                            $imgSrc = null;
                            if (! empty($event->image)) {
                                if (\Illuminate\Support\Str::startsWith($event->image, ['http://', 'https://'])) {
                                    $imgSrc = $event->image;
                                } else {
                                    // If the public/storage symlink exists and file is accessible, use it
                                    if (file_exists(public_path('storage/' . $event->image))) {
                                        $imgSrc = asset('storage/' . $event->image);
                                    } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($event->image)) {
                                        // storage file exists but symlink missing — use controller route that streams it
                                        $enc = rtrim(strtr(base64_encode($event->image), '+/', '-_'), '=');
                                        $imgSrc = url('/storage-image/' . $enc);
                                    } else {
                                        // fallback placeholder
                                        $imgSrc = asset('newAdmin/images/pa.png');
                                    }
                                }
                            } else {
                                $imgSrc = asset('newAdmin/images/pa.png');
                            }
                        @endphp
                        <a href="{{ url('registrations/create') . '?event=' . $event->id }}" style="text-decoration:none;color:inherit;">
                            <div class="event-img-wrapper">
                                <img src="{{ $imgSrc }}" alt="{{ $event->name }}">
                            </div>
                        </a>
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
