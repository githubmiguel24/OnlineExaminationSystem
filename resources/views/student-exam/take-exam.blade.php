@extends('common.main')

@section('title', 'Take Exam')

@section('content')
<div class="container-fluid py-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Exam Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold">{{ $exam->title }}</h1>
                    <p class="text-muted">{{ $exam->subject_name }}</p>
                </div>
                <div>
                    <span class="badge bg-secondary fs-6">{{ $exam->duration_minutes }} min</span>
                    <span class="badge bg-primary fs-6 ms-2">{{ count($questions) }} questions</span>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST" action="{{ route('studentExam.submit', $exam->exam_id) }}" id="examForm">
                        @csrf

                        @foreach($questions as $index => $question)
                            <div class="mb-4 p-3 border rounded-3 bg-light">
                                <div class="d-flex align-items-start">
                                    <span class="fw-bold text-primary me-2">{{ $index + 1 }}.</span>
                                    <div>
                                        <p class="fw-bold mb-2">{{ $question->quest_desc }}</p>
                                        <div class="ms-3">
                                            @foreach($question->choices as $key => $choice)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="radio"
                                                           name="answers[{{ $question->question_id }}]"
                                                           value="{{ $key }}"
                                                           id="q{{ $question->question_id }}_{{ $key }}" required>
                                                    <label class="form-check-label" for="q{{ $question->question_id }}_{{ $key }}">
                                                        {{ $choice }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('studentExam.instructions', $exam->exam_id) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Instructions
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle"></i> Submit Exam
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="mt-3 text-center">
                <small class="text-muted">Please answer all questions before submitting.</small>
            </div>

        </div>
    </div>
</div>
@endsection