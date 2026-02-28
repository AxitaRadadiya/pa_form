@extends('admin.layouts.app')
@section('title', 'Manage Event')
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
            <h4 class="mb-0 font-bold mt-2">Event</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Event</li>
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
                    <a href="#" data-toggle="modal" data-target="#event-create" class="btn btn-primary waves-effect waves-light btn-sm float-right">Add New Event</a>
            </div>
            <div class="card-body">
                <table id="EventTable" class="table dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Event Name</th>
                            <th>Image</th>
                            <th>QR</th>
                            <th>Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Sr No.</th>
                            <th>Event Name</th>
                            <th>Image</th>
                            <th>QR</th>
                            <th>Note</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="event-create" tabindex="-1" role="dialog" aria-labelledby="eventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventLabel">Add / Edit Event</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="eventForm" action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                    @csrf

                    <div class="row">
                        <div class="col-md-12">
                            <label>Event Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>QR Code (upload)</label>
                            <input type="file" class="form-control" name="qr_code" accept="image/*">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveEvent">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('pageScript')
<script>
    $(document).ready(function () {
        // Lead-related JavaScript removed for Events page.
    });
</script>
@endsection