@extends('common.main')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-2 col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <nav class="nav flex-column nav-pills flex-grow-1">
                        <a href="{{ route('studentAuth.dashboard') }}"
                           class="nav-link {{ request()->routeIs('studentAuth.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                        </a>
                        <a href="{{ route('student.results') }}"
                           class="nav-link {{ request()->routeIs('student.results*') ? 'active' : '' }}">
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

            {{-- Topbar --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <p class="text-muted small mb-0">{{ now()->format('l, F j, Y') }}</p>
                    <h1 class="h3 fw-bold">
                        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
                        {{$student->full_name}}!
                    </h1>
                    <p class="text-muted">email: {{ $student->email }}</p>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success me-3" style="width:48px;height:48px;">
                                    <i class="bi bi-check2-circle fs-2"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Exams Taken</p>
                                    <h3 class="fw-bold mb-0">{{ $examsTaken }}</h3>
                                    <span class="text-muted small">Completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success me-3" style="width:48px;height:48px;">
                                    <i class="bi bi-play-circle-fill fs-2"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Ongoing Exams</p>
                                    <h3 class="fw-bold mb-0">{{ $ongoingExams }}</h3>
                                    <span class="text-muted small">Available to take now</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-8">

                {{--Take Exam by Access Code --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-key-fill text-primary me-2"></i>Take Exam by Access Code
                        </h5>
                        <form method="POST" action="{{ route('student.exam.by-code') }}">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="access_code" class="form-control"
                                        placeholder="Enter exam access code (e.g. KVCLZK)" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-play-fill"></i> Start Exam
                                </button>
                            </div>
                        </form>
                        <small class="text-muted">Enter the access code provided by your teacher.</small>
                    </div>
                </div>

                {{-- 2. Recent Results --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart-fill text-primary me-2"></i>Recent Results
                            </h5>
                            <a href="{{ route('student.results') }}" class="btn btn-sm btn-outline-secondary">
                                View All <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        @if($recentResults->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-clipboard-x fs-1"></i>
                                <p class="mt-2 mb-0">No results yet.</p>
                            </div>
                        @else
                            @foreach($recentResults as $res)
                                <a href="{{ route('student.results.show', $res->take_id) }}"
                                    class="d-block text-decoration-none border rounded-3 p-3 mb-2"
                                    onmouseover="this.classList.add('border-primary')"
                                    onmouseout="this.classList.remove('border-primary')">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="fw-bold text-dark mb-0 text-truncate">{{ $res->exam_title }}</p>
                                            <p class="text-muted small mb-1">{{ $res->subject_name }}</p>
                                        </div>
                                        <span class="badge bg-{{ $res->status === 'Passed' ? 'success' : 'danger' }} ms-2">
                                            {{ $res->status }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="text-muted small">
                                            Score: <strong class="text-dark">{{ $res->score }}/{{ $res->total }}</strong>
                                        </span>
                                        <span class="fw-bold small text-{{ $res->status === 'Passed' ? 'success' : 'danger' }}">
                                            {{ $res->percentage }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-{{ $res->status === 'Passed' ? 'success' : 'danger' }}"></div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>


            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">
                
                {{-- Quick Actions --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-lightning-fill text-primary me-2"></i>Quick Actions
                        </h5>

                        <!-- Action: View Results -->
                        <a href="{{ route('student.results') }}" class="d-flex align-items-center gap-3 text-decoration-none p-3 border rounded-3 mb-3"
                            onmouseover="this.classList.add('bg-light')"
                            onmouseout="this.classList.remove('bg-light')">
                            <div class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width:36px;height:36px;">
                                <i class="bi bi-bar-chart-line-fill"></i>
                            </div>
                            <div>
                                <p class="fw-bold text-dark mb-0">View Exam Results</p>
                                <p class="text-muted small mb-0">{{ $examsTaken ?? 0 }} completed</p>
                            </div>
                        </a>

                        <!-- Action: Edit Profile  -->
                        <a href="{{ route('student.profile.edit') }}"
                        class="d-flex align-items-center gap-3 text-decoration-none p-3 border rounded-3"
                        onmouseover="this.classList.add('bg-light')"
                        onmouseout="this.classList.remove('bg-light')">
                            <div class="d-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary" style="width:36px;height:36px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <p class="fw-bold text-dark mb-0">Edit Profile</p>
                                <p class="text-muted small mb-0">Manage account settings</p>
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