<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Details - Skillage CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Skillage CRM</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, {{ session('user_name') }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Lead Details</h2>
                    <div class="btn-group">
                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Lead
                        </a>
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Leads
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Personal Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $lead->title }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $lead->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $lead->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>WhatsApp:</strong></td>
                                        <td>{{ $lead->whatsapp ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gender:</strong></td>
                                        <td>{{ ucfirst($lead->gender) ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Age:</strong></td>
                                        <td>{{ $lead->age ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Lead Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-primary">{{ $lead->leadStatus->title ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Source:</strong></td>
                                        <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Telecaller:</strong></td>
                                        <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Country:</strong></td>
                                        <td>{{ $lead->country->title ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Course:</strong></td>
                                        <td>{{ $lead->course->title ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Place:</strong></td>
                                        <td>{{ $lead->place ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Additional Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Qualification:</strong></td>
                                        <td>{{ $lead->qualification ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>{{ $lead->address ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Interest Status:</strong></td>
                                        <td>{{ $lead->interest_status ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Timestamps</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ $lead->created_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated:</strong></td>
                                        <td>{{ $lead->updated_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
