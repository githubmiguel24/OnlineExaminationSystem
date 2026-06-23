@extends('common.main')
@section('title', 'Student Registration')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header text-center fw-bold">Student Registration</div>
        <div class="card-body">

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('studentAuth.register.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Student Number</label>
                    <input type="text" name="student_number" class="form-control" value="{{ old('student_number') }}" required>
                </div>
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
                <button type="submit" class="btn btn-success w-100">Register</button>
            </form>

            <div class="text-center mt-3">
                Already have an account? <a href="{{ route('studentAuth.login') }}">Login here</a>
            </div>
        </div>
    </div>
</div>
@endsection
