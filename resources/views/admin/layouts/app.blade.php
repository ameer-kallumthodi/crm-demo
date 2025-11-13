@extends('layouts.mantis')

@section('title', 'Admin Panel')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>@yield('page-title', 'Admin Panel')</h5>
                        @yield('page-actions')
                    </div>
                    <div class="card-body">
                        @yield('admin-content')
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>

<!-- Modals -->
@yield('modals')

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }

    // Handle form submissions via AJAX
    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');
        const method = form.attr('method') || 'POST';
        
        // Show loading state
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);
                    
                    // Close modal if it's a modal form
                    const modal = form.closest('.modal');
                    if (modal.length) {
                        modal.modal('hide');
                    }
                    
                    // Reload page or update table
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', response.message || 'An error occurred.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                showAlert('error', errorMessage);
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Handle delete actions
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        
        const url = $(this).data('url');
        const itemName = $(this).data('name') || 'item';
        
        if (confirm(`Are you sure you want to delete this ${itemName}?`)) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('error', response.message || 'An error occurred.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    showAlert('error', errorMessage);
                }
            });
        }
    });

    // Handle edit actions
    $('.edit-btn').on('click', function(e) {
        e.preventDefault();
        
        const url = $(this).data('url');
        const modal = $('#editModal');
        
        $.get(url)
            .done(function(data) {
                // Populate form with data
                const form = modal.find('form');
                Object.keys(data).forEach(function(key) {
                    const field = form.find(`[name="${key}"]`);
                    if (field.length) {
                        if (field.attr('type') === 'checkbox') {
                            field.prop('checked', data[key] == 1 || data[key] === true);
                        } else {
                            field.val(data[key]);
                        }
                    }
                });
                
                // Update form action
                form.attr('action', url);
                form.attr('method', 'PUT');
                
                // Show modal
                modal.modal('show');
            })
            .fail(function() {
                showAlert('error', 'Failed to load data.');
            });
    });
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert
    $('.card-body').prepend(alertHtml);
    
    // Auto-hide after 10 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 10000);
}
</script>
@endpush
@endsection
