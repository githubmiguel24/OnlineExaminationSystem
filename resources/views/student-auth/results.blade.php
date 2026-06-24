@extends('common.main')

@section('title', 'My Results')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-2 col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <nav class="nav flex-column nav-pills flex-grow-1">
                        <a href="{{ route('studentAuth.dashboard') }}"
                           class="nav-link">
                            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                        </a>
                        <a href="{{ route('student.results') }}"
                           class="nav-link active">
                            <i class="bi bi-bar-chart-fill me-2"></i> My Results
                        </a>
                    </nav>
                    <form method="POST" action="{{ route('studentAuth.logout') }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-lg-10 col-md-9">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold">My Results</h1>
                    <p class="text-muted">All your completed exam attempts</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($results->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-x fs-1"></i>
                            <p class="mt-3">You haven't taken any exams yet.</p>
                            <a href="{{ route('studentAuth.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Exam</th>
                                        <th>Subject</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $res)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $res->exam_title }}</td>
                                            <td class="text-muted">{{ $res->subject_name }}</td>
                                            <td>{{ $res->score }}/{{ $res->total }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress" style="width:70px;height:6px;">
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
                                                {{ $res->end_time ? \Carbon\Carbon::parse($res->end_time)->format('M d, Y h:i A') : 'N/A' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('student.results.show', $res->take_id) }}" class="btn btn-sm btn-outline-secondary">
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
    </div>
</div>
@endsection