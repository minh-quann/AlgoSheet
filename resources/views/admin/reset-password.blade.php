<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AlgoSheet :: Reset Password</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/custom.css') }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    @include('admin.message')

    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h3">Administrative Panel</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Reset your password</p>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">Please fix the errors below.</div>
            @endif

            <form action="{{ route('admin.processResetPassword') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-group mb-3">
                    <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('new_password')
                        <p class="invalid-feedback d-block">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="confirm_password" class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Confirm New Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('confirm_password')
                        <p class="invalid-feedback d-block">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                    </div>
                </div>
            </form>

            <p class="mt-3 mb-0">
                <a href="{{ route('admin.login') }}">Back to login</a>
            </p>
        </div>
    </div>
</div>

<script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin-assets/js/adminlte.min.js') }}"></script>
</body>
</html>
