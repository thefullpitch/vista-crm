@extends('admin.layouts.auth')

@section('content')
<div class="card login-card p-0 shadow-lg border-0" style="background: rgba(255,255,255,0.95);">
    <div class="row g-0">
        <!-- Left Side: Vector Illustration -->
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-white p-5 border-end" style="border-radius: 24px 0 0 24px;">
            <div class="text-center">
                <img src="{{ asset('assets/images/login-illustration.jpg') }}" alt="CRM Security Vector" class="img-fluid floating-img" style="max-height: 400px; mix-blend-mode: multiply;">
                <h4 class="fw-bold mt-4" style="color: #4f46e5;">Secure Ecosystem</h4>
                <p class="text-muted small px-3">Advanced telemetry and encryption keeping your enterprise data safe at all times.</p>
            </div>
        </div>
        
        <!-- Right Side: Login Form -->
        <div class="col-md-6 p-4 p-md-5 d-flex align-items-center">
            <div class="w-100">
                <div class="text-center mb-4">
                    <div class="d-inline-block text-white rounded-circle p-3 mb-3 shadow-sm" style="background: var(--primary-gradient);">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Welcome Back</h3>
                    <p class="text-muted small">Vista CRM - Secure Access</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 border-start border-4 border-danger rounded-3 shadow-sm py-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
                            <ul class="mb-0 ps-3 small text-start">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
                    
                    <div class="form-floating mb-4">
                        <input type="email" name="email" class="form-control rounded-pill px-4 shadow-none bg-light border-0" id="floatingEmail" placeholder="name@example.com" required autofocus value="{{ old('email') }}">
                        <label for="floatingEmail" class="px-4 text-muted"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control rounded-pill px-4 shadow-none bg-light border-0" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword" class="px-4 text-muted"><i class="bi bi-key me-2"></i>Password</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input shadow-none" id="remember" name="remember">
                            <label class="form-check-label small text-muted user-select-none" for="remember">Remember me</label>
                        </div>
                        <a href="#" class="text-decoration-none small fw-semibold" style="color: #4f46e5;">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg" style="background: var(--primary-gradient); border: none; transition: transform 0.2s;">
                        Login to Dashboard <i class="bi bi-arrow-right-short ms-1 fs-5 align-middle"></i>
                    </button>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted mb-0">Authorized Personnel Only</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15) !important;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -10px rgba(79, 70, 229, 0.6) !important;
    }
    .floating-img {
        animation: floatImg 6s ease-in-out infinite;
    }
    @keyframes floatImg {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
</style>
@endsection

