@extends('common.main')
@section('title', isset($exam) ? 'Edit Exam Settings' : 'Create Exam Settings')
@section('content')

<div class="container py-4" style="font-family: sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('teacherAuth.dashboard') }}" class="btn btn-outline-primary btn-sm">&larr; Back to Dashboard</a>
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

    <div class="card shadow-sm border-1 mx-auto rounded-3" style="max-width: 700px;">
        <div class="card-body p-4">
            
            <form method="POST" action="{{ isset($exam) ? route('exams.update', $exam->exam_id) : route('exams.store') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Exam Title</label>
                    <input type="text" name="title" class="form-control bg-light border-0" placeholder="e.g., Midterm Examination" value="{{ old('title', $exam->title ?? '') }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Subject Classification</label>
                    <select name="subject_id" class="form-select bg-light border-0" required>
                        <option value="" disabled {{ old('subject_id', $exam->subject_id ?? '') ? '' : 'selected' }}>-- Select a Subject --</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->subject_id }}" {{ old('subject_id', $exam->subject_id ?? '') == $subj->subject_id ? 'selected' : '' }}>{{ $subj->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Description</label>
                    <textarea name="description" class="form-control bg-light border-0" rows="2" placeholder="Brief instructions or details about the exam...">{{ old('description', $exam->description ?? '') }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Duration (Mins)</label>
                        <input type="number" name="duration_minutes" class="form-control bg-light border-0" value="{{ old('duration_minutes', $exam->duration_minutes ?? 60) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Start Date</label>
                        <input type="date" name="start_date" class="form-control bg-light border-0" value="{{ old('start_date', $exam->start_date ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Start Time</label>
                        <input type="time" name="start_time" class="form-control bg-light border-0" value="{{ old('start_time', $exam->start_time ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">End Date</label>
                        <input type="date" name="end_date" class="form-control bg-light border-0" value="{{ old('end_date', $exam->end_date ?? '') }}" required>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3 offset-md-3">
                        <label class="form-label fw-bold text-muted small">End Time</label>
                        <input type="time" name="end_time" class="form-control bg-light border-0" value="{{ old('end_time', $exam->end_time ?? '') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded mb-4 border">
                    <div>
                        <strong class="text-dark">{{ isset($exam) ? 'Publish Status' : 'Publish Immediately' }}</strong>
                        <p class="text-muted mb-0 small" style="font-size: 12px;">
                            {{ isset($exam) ? 'Leave checked to keep this exam published. Uncheck to save as draft.' : 'If checked, students can take this exam once questions are added.' }}
                        </p>
                    </div>
                    <div class="form-check form-switch m-0 p-0">
                        @php
                            $published = old('publish_immediately', isset($exam) ? ($exam->status === 'Published') : true);
                        @endphp
                        <input type="checkbox" name="publish_immediately" value="1" {{ $published ? 'checked' : '' }} style="width: 20px; height: 20px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                    {{ isset($exam) ? 'Update Exam Settings' : 'Save Settings & Proceed to Questions →' }}
                </button>
            </form>

        </div>
    </div>
</div>

@endsection