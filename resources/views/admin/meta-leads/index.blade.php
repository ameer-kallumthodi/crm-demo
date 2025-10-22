@extends('layouts.admin')

@section('title', 'Meta Leads Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Meta Leads Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="fetchLeads()">
                            <i class="fas fa-sync"></i> Fetch Leads
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="pushLeads()">
                            <i class="fas fa-upload"></i> Push to Leads
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="testToken()">
                            <i class="fas fa-check"></i> Test Token
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="debugEnv()">
                            <i class="fas fa-bug"></i> Debug
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 id="total-leads">-</h3>
                                    <p>Total Leads</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="leads-today">-</h3>
                                    <p>Today's Leads</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3 id="leads-with-phone">-</h3>
                                    <p>With Phone</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3 id="leads-with-email">-</h3>
                                    <p>With Email</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filter-name" placeholder="Filter by name">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filter-phone" placeholder="Filter by phone">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filter-email" placeholder="Filter by email">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filter-form">
                                <option value="">All Forms</option>
                                <option value="1">Form 1</option>
                                <option value="2">Form 2</option>
                            </select>
                        </div>
                    </div>

                    <!-- Leads Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="leads-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>City</th>
                                    <th>Form</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Leads pagination">
                            <ul class="pagination" id="pagination">
                                <!-- Pagination will be loaded via AJAX -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lead Details Modal -->
<div class="modal fade" id="leadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="leadDetails">
                <!-- Lead details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    loadLeads();
    loadStatistics();
    
    // Filter functionality
    $('#filter-name, #filter-phone, #filter-email, #filter-form').on('change', function() {
        loadLeads();
    });
});

function loadLeads(page = 1) {
    const filters = {
        name: $('#filter-name').val(),
        phone: $('#filter-phone').val(),
        email: $('#filter-email').val(),
        form_no: $('#filter-form').val(),
        page: page
    };

    $.get('{{ route("admin.meta-leads.index") }}', filters)
        .done(function(response) {
            updateTable(response.data);
            updatePagination(response);
        })
        .fail(function() {
            toastr.error('Failed to load leads');
        });
}

function updateTable(leads) {
    const tbody = $('#leads-table tbody');
    tbody.empty();
    
    leads.forEach(function(lead) {
        const row = `
            <tr>
                <td>${lead.id}</td>
                <td>${lead.full_name || '-'}</td>
                <td>${lead.phone_number || '-'}</td>
                <td>${lead.email || '-'}</td>
                <td>${lead.city || '-'}</td>
                <td>Form ${lead.form_no}</td>
                <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewLead(${lead.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteLead(${lead.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function updatePagination(response) {
    const pagination = $('#pagination');
    pagination.empty();
    
    if (response.last_page > 1) {
        for (let i = 1; i <= response.last_page; i++) {
            const active = i === response.current_page ? 'active' : '';
            const pageItem = `
                <li class="page-item ${active}">
                    <a class="page-link" href="#" onclick="loadLeads(${i})">${i}</a>
                </li>
            `;
            pagination.append(pageItem);
        }
    }
}

function loadStatistics() {
    $.get('{{ route("admin.meta-leads.statistics") }}')
        .done(function(response) {
            $('#total-leads').text(response.total_leads);
            $('#leads-today').text(response.leads_today);
            $('#leads-with-phone').text(response.leads_with_phone);
            $('#leads-with-email').text(response.leads_with_email);
        });
}

function fetchLeads() {
    if (confirm('Are you sure you want to fetch leads from Facebook?')) {
        $.post('{{ route("admin.meta-leads.fetch") }}')
            .done(function(response) {
                toastr.success(response.message);
                loadLeads();
                loadStatistics();
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to fetch leads');
            });
    }
}

function pushLeads() {
    if (confirm('Are you sure you want to push Meta leads to the main leads table?')) {
        $.post('{{ route("admin.meta-leads.push") }}')
            .done(function(response) {
                toastr.success(response.message);
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to push leads');
            });
    }
}

function testToken() {
    $.get('{{ route("admin.meta-leads.test-token") }}')
        .done(function(response) {
            toastr.success(response.message);
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            toastr.error(response.error || 'Token test failed');
        });
}

function debugEnv() {
    $.get('{{ route("admin.meta-leads.debug-env") }}')
        .done(function(response) {
            console.log('Debug Info:', response);
            alert('Debug info logged to console. Check browser console for details.');
        })
        .fail(function() {
            toastr.error('Failed to get debug info');
        });
}

function viewLead(id) {
    $.get('{{ route("admin.meta-leads.show", ":id") }}'.replace(':id', id))
        .done(function(response) {
            const lead = response;
            const details = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <p><strong>Name:</strong> ${lead.full_name || '-'}</p>
                        <p><strong>Phone:</strong> ${lead.phone_number || '-'}</p>
                        <p><strong>Email:</strong> ${lead.email || '-'}</p>
                        <p><strong>Form:</strong> Form ${lead.form_no}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Additional Details</h6>
                        <p><strong>City:</strong> ${lead.city || '-'}</p>
                        <p><strong>Job Title:</strong> ${lead.job_title || '-'}</p>
                        <p><strong>Curriculum:</strong> ${lead.curriculum_type || '-'}</p>
                        <p><strong>Child Name:</strong> ${lead.child_name || '-'}</p>
                        <p><strong>Child Grade:</strong> ${lead.child_grade || '-'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Other Details</h6>
                        <pre>${JSON.stringify(lead.formatted_other_details, null, 2)}</pre>
                    </div>
                </div>
            `;
            $('#leadDetails').html(details);
            $('#leadModal').modal('show');
        })
        .fail(function() {
            toastr.error('Failed to load lead details');
        });
}

function deleteLead(id) {
    if (confirm('Are you sure you want to delete this lead?')) {
        $.ajax({
            url: '{{ route("admin.meta-leads.destroy", ":id") }}'.replace(':id', id),
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            toastr.success(response.message);
            loadLeads();
            loadStatistics();
        })
        .fail(function() {
            toastr.error('Failed to delete lead');
        });
    }
}
</script>
@endsection
