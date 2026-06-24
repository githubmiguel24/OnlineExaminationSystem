@extends('common.main')
@section('title', 'Teacher Registration')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="card mx-auto shadow-sm border-0 p-3" style="max-width: 500px;">
        <div class="card-header bg-white border-0 text-center fw-bold fs-4">Teacher Registration</div>
        <div class="card-body">

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('teacherAuth.register.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">School Email</label>
                    <input type="email" name="email" class="form-control" placeholder="teacher@school.edu.ph" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill mt-3">Register as Teacher</button>
            </form>

            <div class="text-center mt-4">
                Already registered? <a href="{{ route('teacherAuth.login') }}" class="text-primary text-decoration-none">Login here</a>
            </div>
        </div>
    </div>
</div>
@endsection