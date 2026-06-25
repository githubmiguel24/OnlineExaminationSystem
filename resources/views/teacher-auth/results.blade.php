@extends('common.main')

@section('title', 'Results - ' . $exam->exam_title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 fw-bold">Student Results</h1>
        </div>
        <a href="{{ route('teacherAuth.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    {{-- Top bar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between m-3">
        <div>
            <p class="text-muted">
                Exam Title: <span class = "fw-bold text-primary"> {{ $exam->exam_title }}</span> <br> Subject: <span class = "fw-bold text-primary"> {{ $exam->subject_name }}</span> <br>
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('teacher.results', $exam->exam_id) }}" class="d-flex">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                        placeholder="Search student…" value="{{ request('search') }}" />
                </div>
            </form>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="row g-1 m-2">
        <div class="col-sm-3">
            <div class="card h-100 border-1 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Takers</p>
                        <h3 class="fw-bold mb-0">{{ $results->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card h-100 border-1 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Passed</p>
                        <h3 class="fw-bold text-success mb-0">{{ $passCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card h-100 border-1 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Failed</p>
                        <h3 class="fw-bold text-danger mb-0">{{ $failCount }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card h-100 border-1 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                        <i class="bi bi-percent fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Avg. Score</p>
                        <h3 class="fw-bold mb-0">{{ $avgScore }}%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Results table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($results->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x fs-1"></i>
                    <p class="mt-3">No students have taken this exam yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $res)
                                <tr>
                                    <td class="text-muted small">{{ $res->email }}</td>
                                    <td class="fw-bold">{{ $res->full_name }}</td>
                                    <td class="fw-bold">{{ $res->score }}/{{ $res->total }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress" style="width:80px;height:6px;">
                                                <div class="progress-bar bg-{{ $res->status === 'Passed' ? 'success' : 'danger' }}"></div>
                                            </div>
                                            <span class="fw-bold small text-{{ $res->status === 'Passed' ? 'success' : 'danger' }}">
                                                {{ $res->percentage }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $res->status === 'Passed' ? 'success' : 'danger' }}">
                                            {{ $res->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $res->end_time ? \Carbon\Carbon::parse($res->end_time)->format('M d, Y') : '—' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.student.result', $res->take_id) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-eye"></i> View
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