<!--   Core JS Files   -->
<!-- jQuery is already loaded in main layout -->
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Map - removed as not used in current layout -->

<!-- Sweet Alert -->
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>


<!-- Include Toastify -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<!-- Telecaller Tracking Script -->
@if(is_telecaller())
<script src="{{ asset('assets/js/telecaller-tracking.js') }}"></script>
<script>
    // Set user role for tracking script
    window.userRoleId = {{ \App\Helpers\AuthHelper::getRoleId() }};
</script>
@endif

<!-- Custom Scripts -->
<script>
    $(document).ready(function() {
        // Initialize any custom functionality here
        console.log('CRM Dashboard loaded successfully');
        
        // Initialize DataTables globally for all tables
        if ($.fn.DataTable) {
            // Initialize tables with 'datatable' class
            $('.datatable').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        responsive: true,
                        pageLength: 25,
                        order: [[0, 'asc']],
                        columnDefs: [
                            { orderable: false, targets: -1 }
                        ],
                        language: {
                            search: "Search:",
                            lengthMenu: "Show _MENU_ entries",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            paginate: {
                                first: "First",
                                last: "Last",
                                next: "Next",
                                previous: "Previous"
                            }
                        }
                    });
                }
            });
            
            // Initialize specific tables with custom configurations
            initializeSpecificTables();
        }
        
        // Function to initialize specific tables with custom settings
        function initializeSpecificTables() {
            // Converted Leads Table
            if ($('#convertedLeadsTable').length && !$.fn.DataTable.isDataTable('#convertedLeadsTable')) {
                $('#convertedLeadsTable').DataTable({
                    "processing": true,
                    "serverSide": false,
                    "responsive": true,
                    "pageLength": 25,
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "order": [[6, 'desc']], // Sort by converted date descending
                    "columnDefs": [
                        { "orderable": false, "targets": [0, 7] }, // Disable sorting on serial number and actions columns
                        { "searchable": false, "targets": [0, 7] } // Disable searching on serial number and actions columns
                    ],
                    "language": {
                        "processing": "Loading converted leads...",
                        "emptyTable": "No converted leads found",
                        "zeroRecords": "No matching converted leads found",
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    },
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                           '<"row"<"col-sm-12"tr>>' +
                           '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "initComplete": function() {
                        // Hide the original pagination since DataTable handles it
                        $('.d-flex.justify-content-center').hide();
                    }
                });
            }
            
            // Leads Table
            if ($('#leadsTable').length && !$.fn.DataTable.isDataTable('#leadsTable')) {
                $('#leadsTable').DataTable({
                    "processing": true,
                    "serverSide": false,
                    "responsive": true,
                    "pageLength": 25,
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    "columnDefs": [
                        { "orderable": false, "targets": [0, 1] }, // Disable sorting on serial number and actions columns
                        { "searchable": false, "targets": [0, 1] } // Disable searching on serial number and actions columns
                    ],
                    "language": {
                        "processing": "Loading leads...",
                        "emptyTable": "No leads found",
                        "zeroRecords": "No matching leads found",
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });
            }
            
        }
        
        // Initialize sidebar toggle
        $('.toggle-sidebar').click(function() {
            $('body').toggleClass('sidebar-collapse');
        });
        
        // Initialize Bootstrap dropdowns
        $('.dropdown-toggle').dropdown();
        
        // Initialize Bootstrap dropdowns with proper event handling
        $('[data-bs-toggle="dropdown"]').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).next('.dropdown-menu').toggle();
        });
        
        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').hide();
            }
        });
        
        // Initialize search functionality
        $('.btn-search').click(function(e) {
            e.preventDefault();
            var searchTerm = $(this).siblings('input').val();
            if (searchTerm) {
                console.log('Searching for:', searchTerm);
                // Add search functionality here
            }
        });
        
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Initialize popovers
        $('[data-bs-toggle="popover"]').popover();
        
        // Initialize Select2 globally for elements with select2 class
        function initializeGlobalSelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                // Initialize single selects (but not the telecaller dropdown)
                $('.select2:not(#telecaller)').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: 'Select an option...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
                
                // Initialize multiple selects (but not the telecaller dropdown)
                $('.select2-multiple:not(#telecaller)').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: 'Select options...',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
                
                return true;
            } else {
                return false;
            }
        }
        
        // Try to initialize immediately
        if (!initializeGlobalSelect2()) {
            // If failed, retry after a short delay
            setTimeout(initializeGlobalSelect2, 200);
        }
    });

    // Show toast messages from session
    @if(session()->has('message_success'))
        toast_success(`{!! session('message_success') !!}`);
    @endif

    @if(session()->has('message_warning'))
        toast_warning(`{!! session('message_warning') !!}`);
    @endif

    @if(session()->has('message_danger'))
        toast_error(`{!! session('message_danger') !!}`);
    @endif

    @if(session()->has('message_primary'))
        toast_primary(`{!! session('message_primary') !!}`);
    @endif

    // Toast Success
    function toast_success(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "success",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#39B39C",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Toast Warning
    function toast_warning(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "warning",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#F6B84B",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Toast Error
    function toast_error(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "danger",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#EF6547",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Toast Primary
    function toast_primary(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px; color: white; font-weight: 500;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "right",
            className: "primary",
            duration: duration,
            offset: {
                x: 20,
                y: 80
            },
            style: {
                background: "#007bff",
                borderRadius: "8px",
                boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                zIndex: 9999
            }
        }).showToast();
    }

    // Notification system
    function loadNotifications() {
        fetch('{{ route("notifications.api") }}')
            .then(response => response.json())
            .then(data => {
                const notificationList = document.getElementById('notificationList');
                const notificationLoading = document.getElementById('notificationLoading');
                const notificationEmpty = document.getElementById('notificationEmpty');
                const notificationBadge = document.getElementById('notificationBadge');
                
                notificationLoading.style.display = 'none';
                
                if (data.notifications && data.notifications.length > 0) {
                    notificationEmpty.style.display = 'none';
                    
                    // Count unread notifications
                    const unreadCount = data.notifications.filter(n => !n.is_read).length;
                    if (unreadCount > 0) {
                        notificationBadge.textContent = unreadCount;
                        notificationBadge.style.display = 'inline-block';
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                    
                    // Render notifications
                    notificationList.innerHTML = data.notifications.map(notification => `
                        <div class="list-group-item list-group-item-action ${notification.is_read ? '' : 'bg-light'}" 
                             data-notification-id="${notification.id}">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="notification-icon bg-${getNotificationColor(notification.type)} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="ti ti-${getNotificationIcon(notification.type)}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 ${notification.is_read ? 'text-muted' : ''}">${notification.title}</h6>
                                        <small class="text-muted">${notification.created_at}</small>
                                    </div>
                                    <p class="text-body mb-1 ${notification.is_read ? 'text-muted' : ''}">${notification.message}</p>
                                    <small class="text-muted">by ${notification.created_by}</small>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    notificationEmpty.style.display = 'block';
                    notificationBadge.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('notificationLoading').style.display = 'none';
                document.getElementById('notificationEmpty').style.display = 'block';
            });
    }

    function markAllAsRead() {
        // Get all unread notifications
        const unreadNotifications = document.querySelectorAll('.list-group-item.bg-light[data-notification-id]');
        if (unreadNotifications.length === 0) {
            return; // No unread notifications to mark
        }

        // Extract notification IDs from the current notifications
        const notificationIds = [];
        unreadNotifications.forEach(item => {
            const notificationId = item.getAttribute('data-notification-id');
            if (notificationId) {
                notificationIds.push(notificationId);
            }
        });

        if (notificationIds.length === 0) {
            return;
        }

        // Mark all unread notifications as read
        Promise.all(notificationIds.map(id => 
            fetch(`/notifications/${id}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
        ))
        .then(() => {
            // Reload notifications to update read status
            loadNotifications();
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
        });
    }


    function getNotificationColor(type) {
        const colors = {
            'info': 'primary',
            'success': 'success',
            'warning': 'warning',
            'error': 'danger'
        };
        return colors[type] || 'primary';
    }

    function getNotificationIcon(type) {
        const icons = {
            'info': 'info-circle',
            'success': 'check-circle',
            'warning': 'alert-triangle',
            'error': 'x-circle'
        };
        return icons[type] || 'bell';
    }

    // Load notifications when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Only load notifications for non-admin users
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) {
            loadNotifications();
            
            // Mark all notifications as read when page loads
            setTimeout(() => {
                markAllAsRead();
            }, 1000); // Small delay to ensure notifications are loaded
            
            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);
            
            // Mark all notifications as read when dropdown is opened
            notificationDropdown.addEventListener('shown.bs.dropdown', function() {
                // Mark all notifications as read when dropdown is fully shown
                markAllAsRead();
            });
        }
    });
</script>