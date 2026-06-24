@extends('common.main')

@section('title', 'Student Result - ' . $take->full_name)

@section('content')
<div class="container-fluid py-4">
    
{{-- Top bar / Back --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold">Student Performance Report</h1>
        <p class="text-muted">{{ $take->exam_title }} — {{ $take->subject_name }}</p>
    </div>
    <a href="{{ route('teacher.results', $examId) }}" class="btn btn-outline-secondary btn-sm ms-auto">
        <i class="bi bi-arrow-left"></i> Back to Results
    </a>
</div>

    <div class="row g-4 mb-4">
        {{-- Student Info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-person-fill text-primary me-2"></i>Student Information
                    </h5>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                             style="width:52px;height:52px;font-weight:bold;font-size:1.1rem">
                            {{ strtoupper(substr($take->full_name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="mb-0 fw-bold fs-5">{{ $take->full_name }}</p>
                            <p class="mb-0 text-muted small">{{ $take->email }}</p>
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Email</span>
                            <span class="fw-semibold">{{ $take->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Student ID</span>
                            <span class="fw-semibold">{{ $take->student_id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Exam Info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Exam Information
                    </h5>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Exam</span>
                            <span class="fw-semibold text-primary">{{ $take->exam_title }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Subject</span>
                            <span class="fw-semibold">{{ $take->subject_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Date Taken</span>
                            <span>{{ $take->start_time ? \Carbon\Carbon::parse($take->start_time)->format('M d, Y · g:i A') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Duration Used</span>
                            <span>{{ $take->duration ?? $take->duration_minutes }} minutes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Score Breakdown --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-trophy-fill text-primary me-2"></i>Score Breakdown
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-center">
                        <p class="fs-2 fw-bold text-primary mb-0">{{ $score }}</p>
                        <p class="text-muted small mb-0">Score</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-secondary bg-opacity-10 p-3 rounded-3 text-center">
                        <p class="fs-2 fw-bold text-dark mb-0">{{ $total }}</p>
                        <p class="text-muted small mb-0">Total Items</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-{{ $status === 'Passed' ? 'success' : 'danger' }} bg-opacity-10 p-3 rounded-3 text-center">
                        <p class="fs-2 fw-bold text-{{ $status === 'Passed' ? 'success' : 'danger' }} mb-0">{{ $percentage }}%</p>
                        <p class="text-muted small mb-0">Percentage</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-{{ $status === 'Passed' ? 'success' : 'danger' }} bg-opacity-10 p-3 rounded-3 text-center">
                        <p class="fs-2 fw-bold text-{{ $status === 'Passed' ? 'success' : 'danger' }} mb-0">{{ $status }}</p>
                        <p class="text-muted small mb-0">Result</p>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-1 small text-muted">
                <span>Score Progress</span>
                <span>{{ $percentage }}% (Passing: 75%)</span>
            </div>
            <div class="progress" style="height:10px;position:relative;">
                <div class="progress-bar bg-{{ $status === 'Passed' ? 'success' : 'danger' }}"></div>
                <div style="position:absolute;left:75%;top:0;bottom:0;width:2px;background:rgba(0,0,0,.15);"></div>
            </div>
        </div>
    </div>

    {{-- Per-Question Breakdown --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-list-check text-primary me-2"></i>Question Details
            </h5>

            @if($questions->isEmpty())
                <div class="text-center py-4 text-muted">
                    <p>No question data available for this attempt.</p>
                </div>
            @else
                @foreach($questions as $i => $q)
                    @php
                        $bgClass = $q->is_correct ? 'bg-success-subtle' : 'bg-danger-subtle';
                        $borderClass = $q->is_correct ? 'border-success' : 'border-danger';
                        $icon = $q->is_correct ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger';
                    @endphp
                    <div class="p-3 mb-3 border rounded-3 {{ $borderClass }} {{ $bgClass }}">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <p class="fw-bold mb-2">
                                    <span class="text-primary me-1">Q{{ $i + 1 }}.</span>
                                    {{ $q->quest_desc }}
                                </p>

                                @if(!empty($q->choices))
                                    <div class="row g-1 mb-2">
                                        @foreach($q->choices as $letter => $choice)
                                            @php
                                                $isRight = $letter === strtoupper($q->quest_answer);
                                                $isGiven = $letter === strtoupper($q->student_answer ?? '');
                                            @endphp
                                            <div class="col-sm-6">
                                                <div class="p-1 rounded small
                                                            @if($isRight) bg-success text-white @endif
                                                            @if($isGiven && !$isRight) bg-danger text-white @endif
                                                            @if(!$isRight && !$isGiven) bg-light @endif">
                                                    {{ $letter }}. {{ $choice }}
                                                    @if($isRight) <i class="bi bi-check ms-1"></i> @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @if($q->student_answer)
                                        <span class="badge bg-{{ $q->is_correct ? 'success' : 'danger' }}">
                                            Student: {{ strtoupper($q->student_answer) }}
                                        </span>
                                    @endif
                                    @if(!$q->is_correct && $q->quest_answer)
                                        <span class="badge bg-success">
                                            Correct: {{ strtoupper($q->quest_answer) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <i class="bi {{ $icon }} fs-4"></i>
                                <p class="small text-muted mb-0">
                                    {{ $q->earned }}/{{ $q->quest_pts }} pts
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection