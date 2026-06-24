@extends('common.main')

@section('title', 'Exam Instructions')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold mb-3">{{ $exam->title }}</h1>
                    <p class="text-muted">{{ $exam->subject_displayname ?? $exam->subject_name ?? 'Subject' }}</p>

                    <hr>

                    <div class="mb-4">
                        <h5 class="fw-bold">Instructions</h5>
                        <p>
                            {{ $exam->description ?? 'No specific instructions provided. Please read each question carefully and select the best answer.' }}
                        </p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-muted d-block">Duration</small>
                                <span class="fw-bold">{{ $exam->duration_minutes }} minutes</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-muted d-block">Questions</small>
                                <span class="fw-bold">
                                    {{ DB::table('exam_question_table')->where('exam_id', $exam->exam_id)->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-muted d-block">Status</small>
                                <span class="fw-bold text-success">{{ $exam->status }}</span>
                            </div>
                        </div>
                    </div>

                    @if(isset($alreadySubmitted) && $alreadySubmitted)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            You have already submitted this exam. You cannot retake it.
                        </div>
                        <a href="{{ route('studentAuth.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    @else
                        <form method="GET" action="{{ route('studentExam.start', $exam->exam_id) }}">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-play-fill"></i> Start Exam
                            </button>
                        </form>
                        <p class="text-muted small mt-2 text-center">
                            Once started, you must complete the exam within the time limit.
                        </p>
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('studentAuth.dashboard') }}" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection