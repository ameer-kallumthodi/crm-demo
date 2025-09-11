<!-- [ Footer ] start -->
<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col my-1 my-md-0">
                <p class="m-0">© {{ date('Y') }} <a href="#" target="_blank">Base CRM</a> All rights reserved.</p>
            </div>
            <div class="col-auto my-1 my-md-0">
                <ul class="list-inline footer-link mb-0">
                    <li class="list-inline-item"><a href="#">Home</a></li>
                    <li class="list-inline-item"><a href="#">Documentation</a></li>
                    <li class="list-inline-item"><a href="#">Support</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- [ Footer ] end -->

<!-- Include Toastify -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script type="text/javascript">
    // Show toast messages from session
    @if(session()->has('message_success'))
        toast_success("{{ session('message_success') }}");
    @endif

    @if(session()->has('message_warning'))
        toast_warning("{{ session('message_warning') }}");
    @endif

    @if(session()->has('message_danger'))
        toast_error("{{ session('message_danger') }}");
    @endif

    @if(session()->has('message_primary'))
        toast_primary("{{ session('message_primary') }}");
    @endif

    // Toast Success
    function toast_success(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "success",
            duration: duration,
            style: {
                background: "#39B39C",
            }
        }).showToast();
    }

    // Toast Warning
    function toast_warning(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "warning",
            duration: duration,
            style: {
                background: "#F6B84B"
            }
        }).showToast();
    }

    // Toast Error
    function toast_error(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "danger",
            duration: duration,
            style: {
                background: "#EF6547",
            }
        }).showToast();
    }

    // Toast Primary
    function toast_primary(message, duration = 3000) {
        var myToastContent = document.createElement('div');
        myToastContent.innerHTML = '<div style="width:320px;">' + message + '</div>';
        Toastify({
            node: myToastContent,
            gravity: "top",
            position: "center",
            className: "primary",
            duration: duration
        }).showToast();
    }
</script>
