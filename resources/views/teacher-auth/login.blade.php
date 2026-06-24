@extends('common.main')
@section('title', 'Teacher Login')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="mb-4 text-center">
        <a href="{{ route('welcome') }}" class="text-decoration-none text-muted">&larr; Back to Portal</a>
    </div>

    <div class="card mx-auto shadow-sm border-0 p-4" style="max-width: 450px;">
        <div class="card-body">
            <h3 class="fw-bold mb-2">Teacher Portal</h3>
            <p class="text-muted mb-4">Manage exams and monitor students.</p>

            @if(session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('teacherAuth.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="teacher@school.edu.ph" value="{{ old('email') }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill">Sign In</button>
            </form>
            
            <div class="text-center mt-4">
                Don't have a teacher account? <a href="{{ route('teacherAuth.register') }}" class="text-primary text-decoration-none">Register here</a>
            </div>
        </div>
    </div>
</div>
@endsection