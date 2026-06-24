@extends('common.main')
@section('title', 'ExamPortal - Welcome')
@section('content')

<div class="container py-5 d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh; font-family: sans-serif;">
    
    <div class="text-center mb-5">
        <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px; font-size: 24px;">
            <i class="bi bi-book"></i> </div>
        <h1 class="mt-3 fw-bold">ExamPortal</h1>
        <p class="text-muted">Student Examination System</p>
    </div>

    <div class="row w-100 justify-content-center gap-4">
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 p-4">
                <div class="card-body">
                    <div class="bg-light text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h4 class="fw-bold">Student Portal</h4>
                    <p class="text-muted mb-4">Take exams, view results, and track your academic progress.</p>
                    <a href="{{ route('studentAuth.login') }}" class="text-primary text-decoration-none fw-bold">
                        Enter as Student &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 p-4">
                <div class="card-body">
                    <div class="bg-light text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                        <i class="bi bi-building"></i>
                    </div>
                    <h4 class="fw-bold">Teacher Portal</h4>
                    <p class="text-muted mb-4">Manage exams, questions, subjects, and monitor students.</p>
                    <a href="{{ route('teacherAuth.login') }}" class="text-primary text-decoration-none fw-bold">
                        Enter as Teacher &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection