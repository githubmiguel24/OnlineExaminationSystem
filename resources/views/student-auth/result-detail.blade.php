@extends('common.main')

@section('title', 'Exam Result')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Summary Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold">{{ $result->exam_title }}</h1>
                    <p class="text-muted">{{ $result->subject_name }}</p>

                    <div class="text-center py-4">
                        <div class="display-1 fw-bold text-{{ $result->status === 'Passed' ? 'success' : 'danger' }}">
                            {{ $result->percentage }}%
                        </div>
                        <p class="fs-5">Score: {{ $result->score }}/{{ $result->total }}</p>
                        <span class="badge bg-{{ $result->status === 'Passed' ? 'success' : 'danger' }} fs-6 px-4 py-2">
                            {{ $result->status }}
                        </span>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Duration</small>
                            <span class="fw-bold">{{ $result->duration }} minutes</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Date Taken</small>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($result->end_time)->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Status</small>
                            <span class="fw-bold text-{{ $result->status === 'Passed' ? 'success' : 'danger' }}">
                                {{ $result->status }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('studentAuth.dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                        <a href="{{ route('student.results') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list"></i> View All Results
                        </a>
                    </div>
                </div>
            </div>

            {{-- Answer Review --}}
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Answer Review</h5>

                    @foreach($questions as $index => $q)
                        @php
                            $isCorrect = ($q->student_answer == $q->quest_answer);
                            $borderClass = $isCorrect ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle';
                        @endphp
                        <div class="mb-3 p-3 border rounded-3 {{ $borderClass }}">
                            <div class="d-flex align-items-start">
                                <span class="fw-bold me-2">{{ $index + 1 }}.</span>
                                <div class="flex-grow-1">
                                    <p class="fw-bold mb-2">{{ $q->quest_desc }}</p>
                                    <div class="ms-3">
                                        @foreach($q->choices as $key => $choice)
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $key == $q->quest_answer ? 'bg-success' : 'bg-secondary' }}">{{ $key }}</span>
                                                <span>{{ $choice }}</span>

                                                @if($key == $q->student_answer && $key != $q->quest_answer)
                                                    <span class="text-danger fw-bold">✗ Your answer</span>
                                                @elseif($key == $q->student_answer && $key == $q->quest_answer)
                                                    <span class="text-success fw-bold">✓ Correct</span>
                                                @elseif($key == $q->quest_answer)
                                                    <span class="text-success fw-bold">✓ Correct answer</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div class="mt-2">
                                            <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }}">
                                                {{ $isCorrect ? 'Correct' : 'Wrong' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection