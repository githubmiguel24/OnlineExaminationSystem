@extends('common.main')
@section('title', 'Forgot Password')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header text-center fw-bold">Reset Password</div>
        <div class="card-body">

            <p class="text-muted">Enter your student number and email to verify your identity, then set a new password.</p>

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('studentAuth.forgotPassword.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Student Number</label>
                    <input type="text" name="student_number" class="form-control" value="{{ old('student_number') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">Reset Password</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('studentAuth.login') }}">Back to Login</a>
            </div>
        </div>
    </div>
</div>
@endsection