@extends('common.main')
@section('title', 'ExamPortal - Welcome')
@section('content')

<div class="container py-5 d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh; font-family: sans-serif;">
    
    <div class="text-center mb-5">
        <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 70px; height: 70px; font-size: 28px;">
            <i class="bi bi-book"></i>
        </div>
        <h1 class="mt-3 fw-bold text-dark">ExamPortal</h1>
        <p class="text-muted fs-5">Online Examination System</p>
    </div>

    <div class="row w-100 justify-content-center gap-4">
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 p-4 text-center hover-shadow transition rounded-4">
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h4 class="fw-bold">Student Portal</h4>
                    <p class="text-muted mb-4">Take exams, view results, and track your academic progress.</p>
                    <a href="{{ route('studentAuth.login') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                        Enter as Student <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 p-4 text-center hover-shadow transition rounded-4">
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                        <i class="bi bi-building"></i>
                    </div>
                    <h4 class="fw-bold">Teacher Portal</h4>
                    <p class="text-muted mb-4">Manage exams, questions, subjects, and monitor students.</p>
                    <a href="{{ route('teacherAuth.login') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                        Enter as Teacher <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition { transition: all 0.3s ease; }
    .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(111, 66, 193, 0.15)!important; transform: translateY(-5px); }
</style>
@endsection