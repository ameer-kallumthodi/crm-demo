@extends('layouts.mantis')

@section('title', 'Call Log Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Call Log Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('call-logs.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Call Logs
                        </a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Call Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Call ID:</th>
                                    <td>{{ $callLog->id }}</td>
                                </tr>
                                <tr>
                                    <th>Call UUID:</th>
                                    <td>{{ $callLog->call_uuid }}</td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge badge-{{ $callLog->type == 'incoming' ? 'info' : ($callLog->type == 'outgoing' ? 'success' : 'warning') }}">
                                            {{ ucfirst($callLog->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>{!! $callLog->call_status_badge !!}</td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>{{ $callLog->formatted_duration }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Start Time:</th>
                                    <td>{{ $callLog->start_time ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>End Time:</th>
                                    <td>{{ $callLog->end_time ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Agent Number:</th>
                                    <td>{{ $callLog->AgentNumber ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Telecaller:</th>
                                    <td>{{ $callLog->telecaller_name }}</td>
                                </tr>
                                <tr>
                                    <th>Extension:</th>
                                    <td>{{ $callLog->extensionNumber ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Called Number:</th>
                                    <td>{{ $callLog->calledNumber ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Caller Number:</th>
                                    <td>{{ $callLog->callerNumber ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Destination:</th>
                                    <td>{{ $callLog->destinationNumber ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Caller ID:</th>
                                    <td>{{ $callLog->callerid ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($callLog->recording_URL)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Recording</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-volume-up"></i> 
                                <a href="{{ $callLog->recording_URL }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Play Recording
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($lead)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Related Lead Information</h5>
                            <div class="alert alert-success">
                                <strong>Lead Name:</strong> {{ $lead->title }}<br>
                                <strong>Phone:</strong> {{ $lead->code }}{{ $lead->phone }}<br>
                                <strong>Email:</strong> {{ $lead->email ?? 'N/A' }}<br>
                                <strong>Status:</strong> {{ $lead->leadStatus->title ?? 'N/A' }}<br>
                                <strong>Source:</strong> {{ $lead->leadSource->title ?? 'N/A' }}<br>
                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-success mt-2">
                                    <i class="fas fa-eye"></i> View Lead Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>System Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Created At:</th>
                                    <td>{{ $callLog->created_at ? $callLog->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At:</th>
                                    <td>{{ $callLog->updated_at ? $callLog->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                                @if($callLog->createdBy)
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $callLog->createdBy->name }}</td>
                                </tr>
                                @endif
                                @if($callLog->updatedBy)
                                <tr>
                                    <th>Updated By:</th>
                                    <td>{{ $callLog->updatedBy->name }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection