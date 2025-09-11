<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->
<head>
    <title>Login - {{ config('app.name', 'Base CRM') }}</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Base CRM Management System">
    <meta name="keywords" content="CRM, Management, Dashboard, Admin">
    <meta name="author" content="Base CRM">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('assets/mantis/images/favicon.svg') }}" type="image/x-icon">
    
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/tabler-icons.min.css') }}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/feather.css') }}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/fontawesome.css') }}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/material.css') }}">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/mantis/css/style-preset.css') }}">
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
    @include('layouts.parts.loader')
    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="auth-header">
                    <a href="{{ route('dashboard') }}"><img src="{{ asset('assets/mantis/images/logo-dark.svg') }}" alt="img"></a>
                </div>
                <div class="card my-5">
                    <div class="card-body">
                        <div class="mb-4">
                            <h3 class="mb-0"><b>Login</b></h3>
                        </div>
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="auth-footer row">
                    <div class="col my-1">
                        <p class="m-0">Copyright © <a href="#">{{ date('Y') }} Base CRM</a></p>
                    </div>
                    <div class="col-auto my-1">
                        <ul class="list-inline footer-link mb-0">
                            <li class="list-inline-item"><a href="#">Home</a></li>
                            <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                            <li class="list-inline-item"><a href="#">Contact us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    
    <!-- Required Js -->
    <script src="{{ asset('assets/mantis/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/feather.min.js') }}"></script>
</body>
<!-- [Body] end -->
</html>