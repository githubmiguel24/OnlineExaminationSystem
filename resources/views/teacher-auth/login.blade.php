@extends('common.main')
@section('title', 'Teacher Login')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="mb-4 text-center">
        <a href="{{ route('welcome') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
    </div>

    <div class="card mx-auto shadow-sm border-1 p-4 rounded-4" style="max-width: 450px;">
        <div class="card-body">
            
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <h3 class="fw-bold mb-1">Teacher Portal</h3>
                <p class="text-muted small">Create and Manage Exams for Students.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success text-center border-0 small"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger text-center border-0 small"><i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger border-0 small"><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('teacherAuth.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">Email Address</label>
                    <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="teacher@school.edu.ph" value="{{ old('email') }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted small">Password</label>
                    <input type="password" name="password" class="form-control bg-light border-0 py-2" placeholder="Enter your password" required>
                </div>
                <div class="text-end mb-4">
                    <a href="{{ route('teacherAuth.forgotPassword') }}" class="text-decoration-none text-primary small fw-semibold">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Sign In</button>
            </form>
            
            <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">Don't have a teacher account?</span>
                <a href="{{ route('teacherAuth.register') }}" class="text-primary text-decoration-none fw-bold small ms-1">Register here</a>
            </div>
        </div>
    </div>
</div>
@endsection