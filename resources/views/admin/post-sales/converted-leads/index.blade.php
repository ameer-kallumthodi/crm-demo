@extends('layouts.mantis')

@section('title', 'Post-sales Converted Students')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Post-sales Converted Students</h5>
                    <p class="m-b-0 text-muted">Review converted students with quick access to their full history.</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Post-sales Converted Students</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.post-sales.converted-leads.index') }}" id="dateFilterForm">
                    <div class="row g-3 align-items-end">
                        <!-- Search -->
                        <div class="col-6 col-md-4 col-lg-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control form-control-sm" name="search" id="search"
                                value="{{ request('search') }}" placeholder="Name, phone, email or register no.">
                        </div>

                        <!-- Course -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select form-select-sm" name="course_id" id="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- BDE -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="telecaller_id" class="form-label">BDE</label>
                            <select class="form-select form-select-sm" name="telecaller_id" id="telecaller_id">
                                <option value="">All BDEs</option>
                                @foreach($telecallers as $telecaller)
                                <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                    {{ $telecaller->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- From Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" id="date_from"
                                value="{{ request('date_from') }}">
                        </div>

                        <!-- To Date -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" id="date_to"
                                value="{{ request('date_to') }}">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 col-lg-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill flex-lg-grow-0">
                                    <i class="ti ti-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.post-sales.converted-leads.index') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-lg-grow-0">
                                    <i class="ti ti-x me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- Desktop Header -->
                <div class="d-none d-md-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Converted Students</h5>
                </div>

                <!-- Mobile Header -->
                <div class="d-md-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Converted Students</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover" id="postSalesConvertedTable" style="min-width: 1800px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>BDE Name</th>
                                    <th>Converted Date</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Admission Batch</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Paid Status</th>
                                    <th>Call Status</th>
                                    <th>Called Date</th>
                                    <th>Call Time</th>
                                    <th>Followup Date</th>
                                    <th>Remark</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <br>
                <hr>
                <br>

                <!-- Mobile Card View -->
                <div class="d-lg-none" id="mobileConvertedStudentsContainer">
                    <!-- Data will be loaded via AJAX with lazy loading -->
                </div>

            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@php
// Build columns array for DataTables
$columns = [
    ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false],
    ['data' => 'name', 'name' => 'name'],
    ['data' => 'phone', 'name' => 'phone'],
    ['data' => 'email', 'name' => 'email'],
    ['data' => 'bde_name', 'name' => 'bde_name', 'orderable' => false, 'searchable' => false],
    ['data' => 'created_at', 'name' => 'created_at'],
    ['data' => 'course', 'name' => 'course', 'orderable' => false, 'searchable' => false],
    ['data' => 'batch', 'name' => 'batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'admission_batch', 'name' => 'admission_batch', 'orderable' => false, 'searchable' => false],
    ['data' => 'subject', 'name' => 'subject', 'orderable' => false, 'searchable' => false],
    ['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false],
    ['data' => 'paid_status', 'name' => 'paid_status', 'orderable' => false, 'searchable' => false],
    ['data' => 'call_status', 'name' => 'call_status', 'orderable' => false, 'searchable' => false],
    ['data' => 'called_date', 'name' => 'called_date', 'orderable' => false, 'searchable' => false],
    ['data' => 'called_time', 'name' => 'called_time', 'orderable' => false, 'searchable' => false],
    ['data' => 'postsale_followup', 'name' => 'postsale_followup', 'orderable' => false, 'searchable' => false],
    ['data' => 'post_sales_remarks', 'name' => 'post_sales_remarks', 'orderable' => false, 'searchable' => false],
    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
];
@endphp

@endsection

@push('scripts')
<style>
    /* Fix DataTables responsive dropdown icon issue */
    .dtr-control {
        position: relative;
        cursor: pointer;
    }

    .dtr-control:before {
        content: '+';
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 18px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 3px;
        background-color: #f8f9fa;
        color: #666;
        font-weight: bold;
        margin-right: 8px;
    }

    .dtr-control.dtr-expanded:before {
        content: '-';
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    /* Improve table responsiveness */
    .table-responsive {
        border: none;
    }

    #postSalesConvertedTable {
        margin-bottom: 0;
    }

    #postSalesConvertedTable thead th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    #postSalesConvertedTable tbody td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .cancelled-row > td {
        background-color: #f8d7da !important;
    }

    .cancelled-card {
        border: 1px solid #f5c2c7;
        background-color: #fff5f5;
    }

    /* Improve mobile card layout */
    @media (max-width: 991.98px) {
        .card-body {
            padding: 0.75rem;
        }

        .mobile-card {
            margin-bottom: 0.5rem;
        }
    }
</style>
<div id="postSalesConfig" data-data-url="{{ route('admin.post-sales.converted-leads.data') }}" style="display: none;"></div>
<script type="application/json" id="postSalesConvertedColumnsData">
{!! json_encode($columns) !!}
</script>
<script>
    const postSalesConfigEl = document.getElementById('postSalesConfig');
    const convertedLeadsDataUrl = postSalesConfigEl ? postSalesConfigEl.dataset.dataUrl : '';
    const postSalesConvertedColumns = JSON.parse(document.getElementById('postSalesConvertedColumnsData').textContent || '[]');
    // Initialize DataTables asynchronously to prevent blocking
    $(document).ready(function() {
        // ULTRA-OPTIMIZED DataTables - Performance Critical
        // Prevent global initialization for this table
        $('#postSalesConvertedTable').removeClass('data_table_basic');
        
        // Use setTimeout to defer initialization and allow page to render first
        setTimeout(function() {
            // Destroy existing instance if any
            if ($.fn.DataTable.isDataTable('#postSalesConvertedTable')) {
                $('#postSalesConvertedTable').DataTable().destroy();
            }
            
            // Get filter values from form
            function getFilterParams() {
                return {
                    search: $('#search').val() || '',
                    course_id: $('#course_id').val() || '',
                    telecaller_id: $('#telecaller_id').val() || '',
                    date_from: $('#date_from').val() || '',
                    date_to: $('#date_to').val() || ''
                };
            }
            
            // Store last JSON response for mobile view
            var lastJsonResponse = null;
            
            // Initialize with AJAX - maximum performance optimizations
            var convertedTable = $('#postSalesConvertedTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: convertedLeadsDataUrl,
                    type: 'GET',
                    data: function(d) {
                        // Merge DataTables parameters with filter parameters
                        var filters = getFilterParams();
                        $.extend(d, filters);
                    },
                    dataSrc: function(json) {
                        // Store JSON response for mobile view
                        lastJsonResponse = json;
                        return json.data;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        showToast('Error loading data. Please try again.', 'error');
                    }
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[5, 'desc']], // Sort by created_at (column 5)
                dom: "Bfrtip",
                buttons: ["csv", "excel", "print", "pdf"],
                stateSave: true,
                scrollCollapse: true,
                // Performance optimizations
                autoWidth: false,
                scrollX: true,
                searchHighlight: false,
                columns: postSalesConvertedColumns,
                // Optimize rendering
                drawCallback: function(settings) {
                    // Initialize tooltips for visible rows
                    var api = this.api();
                    $(api.rows({page: 'current'}).nodes()).find('[data-bs-toggle="tooltip"]').tooltip();
                    
                    // Load mobile view data on first draw only
                    if (lastJsonResponse && settings.iDraw === 1) {
                        loadMobileView(lastJsonResponse);
                    }
                },
                language: {
                    processing: "Loading...",
                    emptyTable: "No data available",
                    zeroRecords: "No matching records found",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
            
            // Reload table when filters change
            $('#dateFilterForm').on('submit', function(e) {
                e.preventDefault();
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                convertedTable.ajax.reload();
            });
            
            // Reload on filter change
            $('#search, #course_id, #telecaller_id, #date_from, #date_to').on('change', function() {
                // Reset mobile view state
                mobileViewState.allData = [];
                mobileViewState.currentPage = 1;
                mobileViewState.hasMore = true;
                convertedTable.ajax.reload();
            });
            
            // Mobile view pagination state
            var mobileViewState = {
                currentPage: 1,
                pageSize: 25,
                totalRecords: 0,
                allData: [],
                isLoading: false,
                hasMore: true
            };
            
            // Load all mobile view data from server
            function loadAllMobileViewData(page = 1, append = false) {
                if (mobileViewState.isLoading) return;
                
                mobileViewState.isLoading = true;
                mobileViewState.currentPage = page;
                const container = $('#mobileConvertedStudentsContainer');
                
                if (!append) {
                    container.empty();
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                }
                
                // Show loading indicator only on first load
                if (!append) {
                    container.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading converted students...</p></div>');
                } else {
                    // Show loading on button when appending
                    const btn = $('.load-more-mobile-btn');
                    if (btn.length > 0) {
                        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
                    }
                }
                
                // Prepare request data
                const requestData = {
                    draw: page,
                    start: (page - 1) * mobileViewState.pageSize,
                    length: mobileViewState.pageSize,
                    order: [{column: 5, dir: 'desc'}],
                    search: {value: '', regex: false}
                };
                
                // Merge with filter parameters
                const filters = getFilterParams();
                $.extend(requestData, filters);
                
                // Make AJAX request to load all data
                $.ajax({
                    url: convertedLeadsDataUrl,
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        mobileViewState.isLoading = false;
                        
                        if (!response || !response.data) {
                            if (!append && mobileViewState.allData.length === 0) {
                                container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No converted students found</h5><p>Try adjusting your filters.</p></div></div>');
                            }
                            return;
                        }
                        
                        // Update total records
                        mobileViewState.totalRecords = response.recordsFiltered || response.recordsTotal || 0;
                        
                        // Process and store data
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function(row) {
                                if (row && row.mobile_view) {
                                    try {
                                        const mobileData = typeof row.mobile_view === 'string' ? JSON.parse(row.mobile_view) : row.mobile_view;
                                        if (mobileData && mobileData.id) {
                                            mobileViewState.allData.push({
                                                data: mobileData,
                                                index: row.index || mobileViewState.allData.length + 1
                                            });
                                        }
                                    } catch (e) {
                                        console.error('Error parsing mobile view data:', e, row);
                                    }
                                }
                            });
                        }
                        
                        // Check if there's more data to load
                        mobileViewState.hasMore = mobileViewState.allData.length < mobileViewState.totalRecords;
                        
                        // Render all loaded data
                        renderMobileViewCards();
                        
                        // Always show load more button if there's more data
                        if (mobileViewState.hasMore && mobileViewState.totalRecords > mobileViewState.allData.length) {
                            // Small delay to ensure rendering is complete
                            setTimeout(function() {
                                showLoadMoreButton();
                            }, 100);
                        } else {
                            // Remove load more button if all data is loaded
                            $('.load-more-mobile-btn').parent().remove();
                        }
                    },
                    error: function(xhr, status, error) {
                        mobileViewState.isLoading = false;
                        console.error('Error loading mobile view data:', error);
                        if (!append && mobileViewState.allData.length === 0) {
                            container.html('<div class="text-center py-4"><div class="alert alert-danger"><i class="ti ti-alert-circle me-2"></i>Error loading data. Please try again.</div></div>');
                        }
                    }
                });
            }
            
            // Render all mobile view cards
            function renderMobileViewCards() {
                const container = $('#mobileConvertedStudentsContainer');
                
                // Only clear on first page load
                if (mobileViewState.currentPage === 1) {
                    container.empty();
                }
                
                if (mobileViewState.allData.length === 0 && !mobileViewState.isLoading) {
                    container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No converted students found</h5><p>Try adjusting your filters.</p></div></div>');
                    return;
                }
                
                // Remove existing info before rendering (but keep load more button)
                container.find('.mobile-view-info').remove();
                
                // Clear existing cards only on first page
                if (mobileViewState.currentPage === 1) {
                    container.find('.card[data-student-id]').remove();
                }
                
                // Render all cards (avoid duplicates by checking data-student-id)
                mobileViewState.allData.forEach(function(item) {
                    // Check if card already exists to avoid duplicates
                    const existingCard = container.find('[data-student-id="' + item.data.id + '"]');
                    if (existingCard.length === 0) {
                        const cardHtml = renderMobileCard(item.data, item.index);
                        // Insert before load more button if it exists
                        const loadMoreBtn = container.find('.load-more-mobile-btn').parent();
                        if (loadMoreBtn.length > 0) {
                            loadMoreBtn.before(cardHtml);
                        } else {
                            container.append(cardHtml);
                        }
                    }
                });
                
                // Initialize tooltips for mobile cards
                container.find('[data-bs-toggle="tooltip"]').tooltip();
                
                // Show record count
                updateMobileViewInfo();
            }
            
            // Show load more button
            function showLoadMoreButton() {
                const container = $('#mobileConvertedStudentsContainer');
                if (!container || container.length === 0) {
                    console.error('Mobile container not found');
                    return;
                }
                
                // Calculate remaining records
                const remaining = mobileViewState.totalRecords - mobileViewState.allData.length;
                
                if (remaining <= 0) {
                    // Remove button if no more records
                    $('.load-more-mobile-btn').parent().remove();
                    return;
                }
                
                const existingButton = container.find('.load-more-mobile-btn');
                
                if (existingButton.length > 0) {
                    // Update existing button
                    existingButton.html('<i class="ti ti-arrow-down me-2"></i>Load More (' + remaining + ' remaining)');
                    existingButton.prop('disabled', false).show();
                } else {
                    // Create new button - make it prominent and visible
                    const loadMoreHtml = '<div class="text-center py-4" style="clear: both; border-top: 1px solid #dee2e6; margin-top: 20px;"><button class="btn btn-outline-primary btn-lg load-more-mobile-btn" onclick="loadMoreMobileData()" style="min-width: 250px; padding: 12px 24px; font-size: 16px;"><i class="ti ti-arrow-down me-2"></i>Load More (' + remaining + ' remaining)</button></div>';
                    container.append(loadMoreHtml);
                }
            }
            
            // Load more mobile data
            window.loadMoreMobileData = function() {
                if (mobileViewState.hasMore && !mobileViewState.isLoading) {
                    const nextPage = Math.floor(mobileViewState.allData.length / mobileViewState.pageSize) + 1;
                    const btn = $('.load-more-mobile-btn');
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
                    loadAllMobileViewData(nextPage, true);
                }
            };
            
            // Update mobile view info
            function updateMobileViewInfo() {
                const infoHtml = '<div class="alert alert-info mb-3 mobile-view-info"><small><i class="ti ti-info-circle me-1"></i>Showing ' + mobileViewState.allData.length + ' of ' + mobileViewState.totalRecords + ' converted students</small></div>';
                const container = $('#mobileConvertedStudentsContainer');
                const existingInfo = container.find('.mobile-view-info');
                if (existingInfo.length > 0) {
                    existingInfo.replaceWith(infoHtml);
                } else {
                    container.prepend(infoHtml);
                }
            }
            
            // Load mobile view with current page data (for initial display)
            function loadMobileView(jsonData) {
                if (!jsonData || !jsonData.data) return;
                
                // Update total records
                const newTotalRecords = jsonData.recordsFiltered || jsonData.recordsTotal || 0;
                
                // Always reload if total records changed or if we haven't loaded anything yet
                if (mobileViewState.totalRecords !== newTotalRecords || mobileViewState.allData.length === 0) {
                    mobileViewState.totalRecords = newTotalRecords;
                    mobileViewState.allData = [];
                    mobileViewState.currentPage = 1;
                    mobileViewState.hasMore = true;
                    mobileViewState.isLoading = false;
                    
                    // Load all data for mobile view - start with first page
                    if (mobileViewState.totalRecords > 0) {
                        loadAllMobileViewData(1, false);
                    } else {
                        const container = $('#mobileConvertedStudentsContainer');
                        container.html('<div class="text-center py-4"><div class="text-muted"><i class="ti ti-inbox f-48 mb-3 d-block"></i><h5>No converted students found</h5><p>Try adjusting your filters.</p></div></div>');
                    }
                }
            }
            
            // Render mobile card HTML
            function renderMobileCard(data, index) {
                // Validate data
                if (!data || !data.id) {
                    console.error('Invalid data in renderMobileCard:', data);
                    return '';
                }

                const statusValue = (data.status || '').toString().toLowerCase();
                const isCancelledFlag = Boolean(data.is_cancelled);
                
                // Add data attribute to track student ID and avoid duplicates
                const cardClasses = ['card', 'mb-2'];
                if (statusValue === 'cancel') {
                    cardClasses.push('cancelled-card');
                }
                let cardHtml = '<div class="' + cardClasses.join(' ') + '" data-student-id="' + (data.id || '') + '">';
                
                cardHtml += '<div class="card-body p-3">';
                
                // Header
                cardHtml += '<div class="d-flex align-items-start justify-content-between mb-2">';
                cardHtml += '<div class="d-flex align-items-center flex-grow-1">';
                cardHtml += '<div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">';
                const name = data.name || 'N/A';
                const firstChar = name && name.length > 0 ? name.charAt(0).toUpperCase() : '?';
                cardHtml += '<span class="f-14 fw-bold text-primary">' + firstChar + '</span>';
                cardHtml += '</div>';
                cardHtml += '<div class="flex-grow-1">';
                cardHtml += '<small class="text-muted d-block f-10 mb-1">' + (data.created_at || '') + '</small>';
                cardHtml += '<h6 class="mb-0 fw-bold f-14">' + escapeHtml(name) + '</h6>';
                cardHtml += '<small class="text-muted f-11">#' + (index || '') + ' - ' + escapeHtml(data.register_number || 'No register #') + '</small>';
                cardHtml += '</div></div>';
                
                // Action buttons
                cardHtml += '<div class="d-flex gap-1">';
                const viewRoute = (data.routes && data.routes.view) ? data.routes.view : '#';
                const statusUpdateRoute = (data.routes && data.routes.status_update) ? data.routes.status_update : '#';
                const invoiceRoute = (data.routes && data.routes.invoice) ? data.routes.invoice : null;
                const cancelFlagRoute = (data.routes && data.routes.cancel_flag) ? data.routes.cancel_flag : null;
                cardHtml += '<a href="' + viewRoute + '" class="btn btn-sm btn-outline-primary" title="View Details"><i class="ti ti-eye f-12"></i></a>';
                if (invoiceRoute) {
                    cardHtml += '<a href="' + invoiceRoute + '" class="btn btn-sm btn-success" title="View Invoice"><i class="ti ti-receipt f-12"></i></a>';
                }
                cardHtml += '<button type="button" class="btn btn-sm btn-outline-success" title="Status Update" onclick="show_ajax_modal(\'' + statusUpdateRoute + '\', \'Status Update\')"><i class="ti ti-edit f-12"></i></button>';
                if (cancelFlagRoute && statusValue === 'cancel') {
                    const cancelBtnClass = isCancelledFlag ? 'btn-danger' : 'btn-outline-danger';
                    const cancelBtnTitle = isCancelledFlag ? 'Update cancellation confirmation' : 'Confirm cancellation';
                    cardHtml += '<button type="button" class="btn btn-sm ' + cancelBtnClass + '" title="' + cancelBtnTitle + '" onclick="show_ajax_modal(\'' + cancelFlagRoute + '\', \'Cancellation Confirmation\')"><i class="ti ti-ban f-12"></i></button>';
                }
                cardHtml += '</div></div>';
                
                // Student details
                cardHtml += '<div class="row g-1 mb-2">';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-phone f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.phone || '-') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-mail f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.email || '-') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-user f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.bde_name || 'Unassigned') + '</small></div></div>';
                cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-book f-12 text-muted me-1"></i><small class="text-muted f-11">' + escapeHtml(data.course || '-') + '</small></div></div>';
                if (data.batch && data.batch !== 'N/A') {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-calendar f-12 text-muted me-1"></i><small class="text-muted f-11">Batch: ' + escapeHtml(data.batch) + '</small></div></div>';
                }
                if (data.admission_batch && data.admission_batch !== 'N/A') {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-calendar-check f-12 text-muted me-1"></i><small class="text-muted f-11">Admission: ' + escapeHtml(data.admission_batch) + '</small></div></div>';
                }
                if (data.subject && data.subject !== 'N/A') {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-bookmark f-12 text-muted me-1"></i><small class="text-muted f-11">Subject: ' + escapeHtml(data.subject) + '</small></div></div>';
                }
                if (statusValue === 'cancel') {
                    const cancelStateLabel = isCancelledFlag ? 'Confirmed' : 'Cancelled';
                    const cancelStateClass = isCancelledFlag ? 'bg-danger' : 'bg-secondary';
                    cardHtml += '<div class="col-12"><span class="badge bg-danger me-1">Cancel</span><span class="badge ' + cancelStateClass + '">Flag: ' + cancelStateLabel + '</span></div>';
                }
                if (data.called_date) {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-calendar-time f-12 text-muted me-1"></i><small class="text-muted f-11">Called: ' + escapeHtml(data.called_date) + '</small></div></div>';
                }
                if (data.called_time) {
                    cardHtml += '<div class="col-6"><div class="d-flex align-items-center"><i class="ti ti-clock f-12 text-muted me-1"></i><small class="text-muted f-11">Call Time: ' + escapeHtml(data.called_time) + '</small></div></div>';
                }
                cardHtml += '</div>';
                
                cardHtml += '</div></div>';
                
                return cardHtml;
            }
            
            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }
        }, 50); // Small delay to allow page to render first
    });

    // Function to show toast notifications
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        // Add to toast container or create one
        let toastContainer = $('.toast-container');
        if (toastContainer.length === 0) {
            toastContainer = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            $('body').append(toastContainer);
        }
        
        toastContainer.append(toast);
        
        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Remove toast element after it's hidden
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
</script>
@endpush
