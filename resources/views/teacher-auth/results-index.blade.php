@extends('common.main')

@section('title', 'Results Overview')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold">Results Overview</h1>
        </div>
        <a href="{{ route('teacherAuth.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($exams->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x fs-1"></i>
                    <p class="mt-3">You haven't created any exams yet.</p>
                    <a href="{{ route('exams.create') }}" class="btn btn-primary">Create Exam</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Exam Title</th>
                                <th>Subject</th>
                                <th>Takers</th>
                                <th>Passed</th>
                                <th>Pass Rate</th>
                                <th>Avg. Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $exam->title }}</div>
                                        @if($exam->description)
                                            <div class="text-muted small">{{ Str::limit($exam->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $exam->subject_name }}</td>
                                    <td>{{ $exam->takers }}</td>
                                    <td>{{ $exam->passed }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress" style="width:60px;height:6px;">
                                                <div class="progress-bar bg-{{ $exam->pass_rate >= 75 ? 'success' : 'warning' }}"></div>
                                            </div>
                                            <span class="fw-bold small">{{ $exam->pass_rate }}%</span>
                                        </div>
                                    </td>
                                    <td>{{ $exam->avg_score }}/{{ $exam->total_attempts > 0 ? '?' : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('teacher.results', $exam->exam_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-bar-chart"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection