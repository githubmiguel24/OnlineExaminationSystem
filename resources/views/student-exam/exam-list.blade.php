@extends('common.main')
@section('title', 'Available Exams')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>Logged in as <strong>{{ session('student')->name }}</strong></div>
        <div class="d-flex gap-2">
            <a href="{{ route('studentAuth.dashboard') }}" class="btn btn-sm btn-outline-primary">Dashboard</a>
            <form method="POST" action="{{ route('studentAuth.logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>

    <h2 class="text-center mb-4">Available Exams</h2>

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <div class="row">
        @forelse($exams as $exam)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $exam->title }}</h5>
                        <p class="card-text">{{ $exam->description }}</p>
                        <p class="mb-3"><strong>Duration:</strong> {{ $exam->duration_minutes }} minutes</p>

                        @if(in_array($exam->id, $takenExamIds))
                            <button class="btn btn-secondary w-100 mt-auto" disabled>Already Submitted</button>
                        @else
                            <a href="{{ route('studentExam.instructions', $exam->id) }}" class="btn btn-primary w-100 mt-auto">View Instructions</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">No exams available right now.</p>
        @endforelse
    </div>
</div>
@endsection