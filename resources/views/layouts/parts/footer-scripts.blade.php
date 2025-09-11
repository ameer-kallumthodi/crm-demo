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

<!-- jQuery Vector Map -->
<script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jsvectormap/map.js') }}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="{{ asset('assets/js/setting-demo.js') }}"></script>
<script src="{{ asset('assets/js/demo.js') }}"></script>

<!-- Custom Scripts -->
<script>
    $(document).ready(function() {
        // Initialize any custom functionality here
        console.log('KaiAdmin template loaded successfully');
        
        // Initialize sidebar toggle
        $('.toggle-sidebar').click(function() {
            $('body').toggleClass('sidebar-collapse');
        });
        
        // Initialize dropdowns
        $('.dropdown-toggle').dropdown();
        
        // Initialize search functionality
        $('.btn-search').click(function(e) {
            e.preventDefault();
            var searchTerm = $(this).siblings('input').val();
            if (searchTerm) {
                console.log('Searching for:', searchTerm);
                // Add search functionality here
            }
        });
    });
</script>