<!-- [ Footer ] start -->
<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col my-1 my-md-0">
                <p class="m-0">© {{ date('Y') }} <a href="#" target="_blank">{{ \App\Models\Setting::get('site_name', config('app.name', '')) }}</a> All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<!-- [ Footer ] end -->

<!-- Toastify is included in footer-scripts.blade.php -->
