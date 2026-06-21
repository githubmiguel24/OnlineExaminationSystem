@extends('common.main')
@section('title', 'Student Dashboard')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, {{ $student->name }}!</h2>
        <form method="POST" action="{{ route('studentAuth.logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
        </form>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center p-3">
                <h6 class="text-muted">Available Exams</h6>
                <p class="fs-3 fw-bold mb-0">{{ $totalExams }}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center p-3">
                <h6 class="text-muted">Exams Taken</h6>
                <p class="fs-3 fw-bold mb-0">{{ $takenExams }}</p>
            </div>
        </div>
    </div>

    <a href="{{ route('studentExam.list') }}" class="btn btn-primary">View Exams</a>
</div>
@endsection