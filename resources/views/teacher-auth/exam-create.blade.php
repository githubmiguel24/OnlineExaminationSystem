@extends('common.main')
@section('title', 'Create Exam Settings')
@section('content')

<div class="container py-4" style="font-family: sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-secondary m-0">Step 1: Exam Settings</h3>
        <a href="{{ route('teacherAuth.dashboard') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Dashboard</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger p-3 small">
            <ul class="m-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 mx-auto rounded-3" style="max-width: 700px;">
        <div class="card-body p-4">
            
            <form method="POST" action="{{ route('exams.store') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Exam Title</label>
                    <input type="text" name="title" class="form-control bg-light border-0" placeholder="e.g., Midterm Examination" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Subject Classification</label>
                    <select name="subject_id" class="form-select bg-light border-0" required>
                        <option value="" disabled selected>-- Select a Subject --</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->subject_id }}">{{ $subj->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Description</label>
                    <textarea name="description" class="form-control bg-light border-0" rows="2" placeholder="Brief instructions or details about the exam..."></textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Duration (Mins)</label>
                        <input type="number" name="duration_minutes" class="form-control bg-light border-0" value="60" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Start Date</label>
                        <input type="date" name="start_date" class="form-control bg-light border-0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">End Date</label>
                        <input type="date" name="end_date" class="form-control bg-light border-0">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded mb-4 border">
                    <div>
                        <strong class="text-dark">Publish Immediately</strong>
                        <p class="text-muted mb-0 small" style="font-size: 12px;">If checked, students with the code can take this immediately after you add questions.</p>
                    </div>
                    <div class="form-check form-switch m-0 p-0">
                        <input type="checkbox" name="publish_immediately" value="1" checked style="width: 20px; height: 20px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                    Save Settings & Proceed to Questions &rarr;
                </button>
            </form>

        </div>
    </div>
</div>

@endsection