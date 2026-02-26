@extends('admin.layouts.app')
@section('title', 'Activity Logs for ' . $model . ' ID: ' . $id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Activity Logs for {{ $model }} (ID: {{ $id }})</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('activity.logs') }}">Activity</a></li>
                    <li class="breadcrumb-item active">{{ $model }} Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header bg-primary text-white">
        Change Log
    </div>
    <div class="container mt-3">
        <table class="table dt-responsive nowrap">
            <thead style="color:#33999A;">
                <tr>
                    <th>Field</th>
                    <th>Old Value</th>
                    <th>New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($changes['new'] as $key => $newValue)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ is_array($changes['old'][$key] ?? null) ? json_encode($changes['old'][$key]) : ($changes['old'][$key] ?? '-') }}</td>
                        <td class="{{ ($changes['old'][$key] ?? '') != $newValue ? 'text-success fw-bold' : '' }}">
                            {{ is_array($newValue) ? json_encode($newValue) : $newValue }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
