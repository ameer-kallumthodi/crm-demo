@extends('layouts.app')

@section('title', 'Call Log Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Call Log Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.call-logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Call Logs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Call Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Call Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Call ID:</strong></td>
                                            <td>{{ $callLog->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $callLog->type == 'incoming' ? 'success' : ($callLog->type == 'outgoing' ? 'primary' : 'warning') }}">
                                                    {{ ucfirst($callLog->type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>{!! $callLog->call_status_badge !!}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>{{ $callLog->formatted_duration }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Start Time:</strong></td>
                                            <td>{{ $callLog->start_time ? $callLog->start_time->format('H:i:s') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>End Time:</strong></td>
                                            <td>{{ $callLog->end_time ? $callLog->end_time->format('H:i:s') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Call UUID:</strong></td>
                                            <td>{{ $callLog->call_uuid ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Contact Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Telecaller:</strong></td>
                                            <td>{{ $callLog->telecaller_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Agent Number:</strong></td>
                                            <td>{{ $callLog->AgentNumber ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Extension:</strong></td>
                                            <td>{{ $callLog->extensionNumber ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Destination:</strong></td>
                                            <td>{{ $callLog->destinationNumber ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Called Number:</strong></td>
                                            <td>{{ $callLog->calledNumber ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Caller Number:</strong></td>
                                            <td>{{ $callLog->callerNumber ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Caller ID:</strong></td>
                                            <td>{{ $callLog->callerid ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Information (if available) -->
                    @if($callLog->lead)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Related Lead Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Lead Name:</strong><br>
                                            <a href="{{ route('leads.index') }}">{{ $callLog->lead->title }}</a>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Phone:</strong><br>
                                            {{ $callLog->lead->code }}{{ $callLog->lead->phone }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Email:</strong><br>
                                            {{ $callLog->lead->email ?? 'N/A' }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Status:</strong><br>
                                            <span class="badge badge-{{ $callLog->lead->leadStatus->color ?? 'secondary' }}">
                                                {{ $callLog->lead->leadStatus->title ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Recording -->
                    @if($callLog->recording_URL)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Call Recording</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <audio controls class="w-100">
                                            <source src="{{ $callLog->recording_URL }}" type="audio/wav">
                                            <source src="{{ $callLog->recording_URL }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                        <br><br>
                                        <a href="{{ $callLog->recording_URL }}" target="_blank" class="btn btn-primary">
                                            <i class="fas fa-download"></i> Download Recording
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- System Information -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>System Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Created By:</strong><br>
                                            {{ $callLog->createdBy->name ?? 'System' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Created At:</strong><br>
                                            {{ $callLog->created_at->format('Y-m-d H:i:s') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Updated At:</strong><br>
                                            {{ $callLog->updated_at->format('Y-m-d H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
