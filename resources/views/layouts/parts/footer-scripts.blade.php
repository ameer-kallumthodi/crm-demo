<!--   Core JS Files   -->
<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
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

<!-- Custom Scripts -->
<script>
    $(document).ready(function() {
        // Initialize any custom functionality here
        console.log('CRM Dashboard loaded successfully');
        
        // Initialize DataTables with reinitialization check
        if ($.fn.DataTable) {
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
</script>