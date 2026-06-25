@extends('common.main')
@section('title', 'Student Registration')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="mb-4 text-center"> 
        <a href="{{ route('welcome') }}" class="text-decoration-none text-muted"> 
        <i class="bi bi-arrow-left me-1"></i> Back to Home </a> 
    </div>
    <div class="card mx-auto shadow-sm border-1 p-4 rounded-4" style="max-width: 500px;">
        <div class="card-header bg-white border-0 text-center fw-bold fs-4">Student Registration</div>
        <div class="card-body">
            <div class="text-center mb-4"> 
                <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                    <i class="bi bi-person-plus-fill"></i> 
                </div> 
                    <h3 class="fw-semibold text-muted small">Create an Account</h3> 
            </div>
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('studentAuth.register.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill mt-3">Register</button>
            </form>

            <div class="text-center mt-4">
                Already have an account? <a href="{{ route('studentAuth.login') }}" class="text-primary text-decoration-none">Login here</a>
            </div>
        </div>
    </div>
</div>
@endsection