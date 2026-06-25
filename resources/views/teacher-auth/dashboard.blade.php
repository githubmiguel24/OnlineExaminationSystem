@extends('common.main')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        {{-- Sidebar --}}
            <div class="col-lg-2 col-md-3 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 d-flex flex-column h-100">
                        <nav class="nav flex-column nav-pills flex-grow-1">
                            <a href="{{ route('teacherAuth.dashboard') }}"
                            class="nav-link {{ request()->routeIs('teacherAuth.dashboard') ? 'active' : '' }}">
                                <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                            </a>
                            
                            {{-- NEW: Dedicated Question Bank Link --}}
                            <a href="{{ route('questions.create') }}"
                            class="nav-link {{ request()->routeIs('questions.*') ? 'active' : '' }}">
                                <i class="bi bi-folder2-open me-2"></i> Question Bank
                            </a>

                            <a href="{{ route('teacher.results.index') }}"
                            class="nav-link {{ request()->routeIs('teacher.results.index*') ? 'active' : '' }}">
                                <i class="bi bi-bar-chart-fill me-2"></i> View Results
                            </a>
                        </nav>
                        <form method="POST" action="{{ route('teacherAuth.logout') }}" class="mt-auto m-0">
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

            {{-- Topbar --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <p class="text-muted small mb-0">{{ now()->format('l, F j, Y') }}</p>
                    <h1 class="fw-bold">Welcome back</h1>
                    <h3 class="text-muted">{{ $teacher->full_name }}</h3>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary me-3" style="width:48px;height:48px;">
                                    <i class="bi bi-people-fill fs-2"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Total Students</p>
                                    <h3 class="fw-bold mb-0">{{ $totalStudents }}</h3>
                                    <span class="text-muted small">Across all exams</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success me-3" style="width:48px;height:48px;">
                                    <i class="bi bi-journal-bookmark-fill fs-2"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Total Subjects</p>
                                    <h3 class="fw-bold mb-0">{{ $totalSubjects }}</h3>
                                    <span class="text-muted small">With exams</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center rounded bg-warning bg-opacity-10 text-warning me-3" style="width:48px;height:48px;">
                                    <i class="bi bi-file-earmark-text-fill fs-2"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Total Exams</p>
                                    <h3 class="fw-bold mb-0">{{ $totalExams }}</h3>
                                    <span class="text-muted small">Created by you</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- Left Column --}}
                <div class="col-lg-8">

                    {{-- Exams List --}}
                    <div class="card border-0 shadow-sm" id="examsSection">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-pencil-square text-primary me-2"></i>Exams
                                </h5>
                                <a href="{{ route('exams.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus"></i> Create Exam 
                                </a>
                            </div>
                        <div class="d-flex flex-wrap mb-2 gap-2">
                            <form method="GET" action="{{ route('teacherAuth.dashboard') }}" class="d-flex">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                        placeholder="Search exams" value="{{ $search }}" />
                                </div>
                                </form>
                        </div>

                            @if($exams->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-clipboard-x fs-1"></i>
                                    <p class="mt-2 mb-0">No exams created yet.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Exam Title</th>
                                                <th>Subject</th>
                                                <th>Start Date & Time</th>
                                                <th>End Date & Time</th>
                                                <th>Status</th>
                                                <th></th>
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
                                                        <div class="text-muted small mt-1">
                                                            Code: <span class="fw-bold">{{ $exam->access_code }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-muted">{{ $exam->subject_name }}</td>
                                                    <td class="text-muted small">
                                                        @if($exam->start_date)
                                                            <i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($exam->start_date . ' ' . ($exam->start_time ?? '00:00'))->format('M d, Y h:i A') }}
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted small">
                                                        @if($exam->end_date)
                                                            <i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($exam->end_date . ' ' . ($exam->end_time ?? '23:59'))->format('M d, Y h:i A') }}
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $exam->status == 'Published' ? 'success' : 'secondary' }}">
                                                            {{ $exam->status }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('teacher.results', $exam->exam_id) }}" class="btn btn-sm btn-outline-primary me-1">
                                                            <i class="bi bi-eye"></i> View Details
                                                        </a>
                                                        <form method="POST" action="{{ route('exams.changeStatus', $exam->exam_id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-{{ $exam->status == 'Published' ? 'danger' : 'success' }}">
                                                                {{ $exam->status == 'Published' ? 'Unpublish' : 'Publish' }}
                                                            </button>
                                                        </form>
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

                {{-- Right Column --}}
                <div class="col-lg-4">

                    {{-- Quick Actions --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-lightning-fill text-primary me-2"></i>Quick Actions
                            </h5>

                            <a href="{{ route('exams.create') }}"
                               class="d-flex align-items-center gap-3 text-decoration-none p-3 border rounded-3 mb-3"
                               onmouseover="this.classList.add('bg-light')"
                               onmouseout="this.classList.remove('bg-light')">
                                <div class="d-flex align-items-center justify-content-center rounded bg-primary text-white" style="width:36px;height:36px;">
                                    <i class="bi bi-file-earmark-plus"></i>
                                </div>
                                <div>
                                    <p class="fw-bold text-dark mb-0">Create New Exam</p>
                                    <p class="text-muted small mb-0">MCQ‑based assessment</p>
                                </div>
                            </a>

                            <a href="{{ route('teacher.results.index') }}" class="d-flex align-items-center gap-3 text-decoration-none p-3 border rounded-3"
                            onmouseover="this.classList.add('bg-light')"
                            onmouseout="this.classList.remove('bg-light')">
                                <div class="d-flex align-items-center justify-content-center rounded bg-warning bg-opacity-10 text-warning" style="width:36px;height:36px;">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div>
                                    <p class="fw-bold text-dark mb-0">View Student Results</p>
                                    <p class="text-muted small mb-0">Overview of all exams</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection