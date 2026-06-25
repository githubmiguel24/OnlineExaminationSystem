@extends('common.main')
@section('title', 'Reset Password – Teacher')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="mb-4 text-center">
        {{-- Back to Login uses teacher route --}}
        <a href="{{ route('teacherAuth.login') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back to Login
        </a>
    </div>

    <div class="card mx-auto shadow-sm border-1 p-4 rounded-4" style="max-width: 450px;">
        <div class="card-body">
            
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h3 class="fw-bold mb-1">Reset Password</h3>
                <p class="text-muted small">Enter your teacher email and a new password.</p>
            </div>

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger border-0 small"><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            @endif

            {{-- Form action uses teacher route --}}
            <form method="POST" action="{{ route('teacherAuth.forgotPassword.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">Email Address</label>
                    <input type="email" name="email" class="form-control bg-light border-1 py-2" placeholder="teacher@school.edu.ph" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">New Password</label>
                    <input type="password" name="password" class="form-control bg-light border-1 py-2" placeholder="Enter new password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted small">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control bg-light border-1 py-2" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Reset Password</button>
            </form>

        </div>
    </div>
</div>
@endsection