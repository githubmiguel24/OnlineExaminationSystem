@extends('common.main')
@section('title', 'Edit Profile')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="mb-4 text-center">
        <a href="{{ route('studentAuth.dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card mx-auto shadow-sm border-1 p-4 rounded-4" style="max-width: 500px;">
        <div class="card-header bg-white border-0 text-center fw-bold fs-4">Edit Profile</div>
        <div class="card-body">
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h3 class="fw-semibold text-muted small">Update your account details</h3>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 small"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger border-0 small"><i class="bi bi-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('student.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">Full Name</label>
                    <input type="text" name="full_name" class="form-control bg-light border-0 py-2" 
                           value="{{ old('full_name', $student->full_name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">Email Address</label>
                    <input type="email" name="email" class="form-control bg-light border-0 py-2" 
                           value="{{ old('email', $student->email) }}" required>
                </div>

                <hr>
                <p class="text-muted small">Leave password fields blank to keep current password.</p>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted small">New Password</label>
                    <input type="password" name="password" class="form-control bg-light border-0 py-2" 
                           placeholder="Enter new password (optional)">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted small">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-2" 
                           placeholder="Confirm new password">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Update Profile</button>
            </form>
        </div>
    </div>
</div>
@endsection