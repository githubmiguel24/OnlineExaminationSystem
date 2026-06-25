@extends('common.main')
@section('title', 'Teacher Registration')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="mb-4 text-center">
        <a href="{{ route('welcome') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
    </div>
    <div class="card mx-auto shadow-sm border-1 p-4 rounded-4" style="max-width: 500px;">
        <div class="card-body">
            
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h3 class="fw-bold mb-1">Teacher Registration</h3>
                <p class="text-muted small">Create your account to start managing exams.</p>
            </div>

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger border-0 small"><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('teacherAuth.register.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">Full Name</label>
                    <input type="text" name="name" class="form-control bg-light border-0 py-2" placeholder="e.g. Maria Clara" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">School Email</label>
                    <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="teacher@school.edu.ph" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">Password</label>
                    <input type="password" name="password" class="form-control bg-light border-0 py-2" placeholder="Create a password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted small">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-2" placeholder="Repeat your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Register as Teacher</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">Already registered?</span> 
                <a href="{{ route('teacherAuth.login') }}" class="text-primary text-decoration-none fw-bold small ms-1">Login here</a>
            </div>
        </div>
    </div>
</div>
@endsection