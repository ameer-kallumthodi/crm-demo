<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

<!-- Layout config Js file -->
<script src="{{ asset('assets/js/layout.js') }}"></script>
<!-- Bootstrap Css -->
<!-- <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" /> -->
<!-- Icons Css -->
<!-- <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" /> -->
<!-- App Css-->
<!-- <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" /> -->
<!-- custom Css-->
<!-- <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" /> -->

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Custom Select2 Styling -->
<style>
/* Select2 styling to match Bootstrap theme */
.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    height: 38px;
    padding: 0.375rem 0.75rem;
}

.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 38px;
    padding: 0.375rem 0.75rem;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #0d6efd;
    border: 1px solid #0d6efd;
    border-radius: 0.25rem;
    color: #fff;
    padding: 0.25rem 0.5rem;
    margin: 0.125rem;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff;
    margin-right: 0.25rem;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #fff;
}

.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #0d6efd;
    color: #fff;
}
</style>

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

<!-- Lordicon CSS -->
<link href="https://cdn.lordicon.com/lupuorrc.json" rel="stylesheet" />
<!-- Toastify -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<!-- Custom SweetAlert2 Styles -->
<style>
.swal2-popup-custom {
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
    border: none !important;
    padding: 2rem !important;
}

.swal2-title-custom {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
    color: #2c3e50 !important;
    margin-bottom: 1rem !important;
}

.swal2-content-custom {
    font-size: 1rem !important;
    color: #6c757d !important;
    line-height: 1.5 !important;
}

.swal2-confirm-custom {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 12px 24px !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3) !important;
}

.swal2-confirm-custom:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4) !important;
}

.swal2-cancel-custom {
    background: linear-gradient(135deg, #6c757d, #5a6268) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 12px 24px !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important;
}

.swal2-cancel-custom:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 16px rgba(108, 117, 125, 0.4) !important;
}

.swal2-actions {
    gap: 12px !important;
    margin-top: 1.5rem !important;
}

.swal2-icon.swal2-warning {
    border-color: #f8d7da !important;
    color: #dc3545 !important;
}

.swal2-icon.swal2-warning .swal2-icon-content {
    color: #dc3545 !important;
}

</style>

@stack('styles')
