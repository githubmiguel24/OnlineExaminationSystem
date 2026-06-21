@extends('common.main')
@section('title', 'Exam Instructions')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">
    <div class="card mx-auto" style="max-width: 700px;">
        <div class="card-header text-center fw-bold">{{ $exam->title }} - Instructions</div>
        <div class="card-body">
            <p>{{ $exam->instructions }}</p>

            <ul>
                <li>Duration: {{ $exam->duration_minutes }} minutes</li>
                <li>Once you start, the timer cannot be paused.</li>
                <li>You can only submit this exam once.</li>
            </ul>

            @if($alreadySubmitted)
                <div class="alert alert-warning text-center">You have already submitted this exam.</div>
                <a href="{{ route('studentExam.list') }}" class="btn btn-secondary w-100">Back to Exam List</a>
            @else
                <a href="{{ route('studentExam.start', $exam->id) }}" class="btn btn-success w-100">Start Exam</a>
            @endif
        </div>
    </div>
</div>
@endsection
