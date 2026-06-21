@extends('common.main')
@section('title', 'Student Login')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="card mx-auto" style="max-width: 450px;">
        <div class="card-header text-center fw-bold">Student Login</div>
        <div class="card-body">

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

            <form method="POST" action="{{ route('studentAuth.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('studentAuth.forgotPassword') }}">Forgot Password?</a>
            </div>
            <div class="text-center mt-2">
                Don't have an account? <a href="{{ route('studentAuth.register') }}">Register here</a>
            </div>
        </div>
    </div>
</div>
@endsection