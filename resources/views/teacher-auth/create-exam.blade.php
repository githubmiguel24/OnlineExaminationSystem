@extends('common.main')
@section('title', 'Create New Exam')
@section('content')

<div class="container py-4" style="font-family: sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Create New Exam</h2>
        <a href="{{ route('teacherAuth.dashboard') }}" class="text-decoration-none text-muted">&larr; Back</a>
    </div>

    <div class="d-flex justify-content-center mb-5 position-relative">
        <div class="d-flex justify-content-between w-50">
            <div class="text-center step-indicator" id="indicator-step-1">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">1</div>
                <small class="text-primary fw-bold">Exam Details</small>
            </div>
            <div class="text-center step-indicator" id="indicator-step-2">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">2</div>
                <small class="text-muted">Questions</small>
            </div>
            <div class="text-center step-indicator" id="indicator-step-3">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">3</div>
                <small class="text-muted">Publish Settings</small>
            </div>
        </div>
    </div>

    <form id="createExamForm" method="POST" action="{{ route('exams.store') }}">
        @csrf
        <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
            <div class="card-body p-4">

                <div id="step-1" class="form-step">
                    <h5 class="fw-bold mb-4">Step 1: Exam Details</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Exam Title</label>
                        <input type="text" name="title" id="input-title" class="form-control bg-light border-0" placeholder="e.g. Midterm Examination" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject</label>
                        <select name="subject_id" id="input-subject" class="form-select bg-light border-0" required onchange="filterQuestionsBySubject()">
                            <option value="" disabled selected>Select a subject...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="3" placeholder="Brief exam description..."></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" id="input-duration" class="form-control bg-light border-0" value="60" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Passing Score (%)</label>
                            <input type="number" name="passing_score" class="form-control bg-light border-0" value="75" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="nextStep(2)">Next: Questions &rarr;</button>
                    </div>
                </div>

                <div id="step-2" class="form-step d-none">
                    <h5 class="fw-bold mb-4">Step 2: Questions</h5>

                    <h6 class="fw-bold text-primary mb-3">Select from Question Bank</h6>
                    <div class="bg-light p-3 rounded mb-4" style="max-height: 250px; overflow-y: auto;" id="question-list-container">
                        <p class="text-muted text-center mt-3" id="no-subject-msg">Please select a subject in Step 1 to view questions.</p>
                        
                        @foreach($questions as $question)
                            <div class="form-check question-item mb-2 p-2 bg-white border rounded" data-subject="{{ $question->subject_id }}" style="display: none;">
                                <input class="form-check-input" type="checkbox" name="questions[]" value="{{ $question->question_id }}" id="q_{{ $question->question_id }}">
                                <label class="form-check-label w-100" for="q_{{ $question->question_id }}">
                                    {{ $question->quest_desc }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-success mb-0">Or Create New Questions</h6>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="addNewQuestionBox()">
                            + Add Blank Question
                        </button>
                    </div>
                    
                    <div id="new-questions-container" class="mb-4">
                        <div class="mb-2 position-relative">
                            <textarea name="new_questions[]" class="form-control bg-light border-success" rows="2" placeholder="Type a brand new question here..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="prevStep(1)">&larr; Previous</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4" onclick="nextStep(3)">Next: Publish Settings &rarr;</button>
                    </div>
                </div>

                <div id="step-3" class="form-step d-none">
                    <h5 class="fw-bold mb-4">Step 3: Publish Settings</h5>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control bg-light border-0">
                        </div>
                    </div>

                    <div class="bg-light p-4 rounded mb-4">
                        <h6 class="text-muted mb-3">EXAM SUMMARY</h6>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Title</small>
                                <strong id="summary-title">Untitled Exam</strong>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Subject ID</small>
                                <strong id="summary-subject">-</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Duration</small>
                                <strong id="summary-duration">-</strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded mb-4">
                        <div>
                            <strong>Publish Immediately</strong>
                            <p class="text-muted mb-0 small">Students with the code will be able to take this exam</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input fs-4" type="checkbox" name="publish_immediately" checked>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="prevStep(2)">&larr; Previous</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Publish Exam</button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // --- Step Navigation & UI Logic ---
    function nextStep(step) {
        if(step === 2 && document.getElementById('input-subject').value === "") {
            alert("Please select a subject first!");
            return;
        }

        if(step === 3) {
            document.getElementById('summary-title').innerText = document.getElementById('input-title').value || 'Untitled';
            document.getElementById('summary-subject').innerText = document.getElementById('input-subject').options[document.getElementById('input-subject').selectedIndex].text;
            document.getElementById('summary-duration').innerText = document.getElementById('input-duration').value + ' minutes';
        }

        document.querySelectorAll('.form-step').forEach(el => el.classList.add('d-none'));
        document.getElementById('step-' + step).classList.remove('d-none');
        updateIndicators(step);
    }

    function prevStep(step) {
        document.querySelectorAll('.form-step').forEach(el => el.classList.add('d-none'));
        document.getElementById('step-' + step).classList.remove('d-none');
        updateIndicators(step);
    }

    function updateIndicators(activeStep) {
        for(let i=1; i<=3; i++) {
            let indicator = document.getElementById('indicator-step-' + i);
            let circle = indicator.querySelector('.rounded-circle');
            let text = indicator.querySelector('small');
            
            if(i <= activeStep) {
                circle.classList.replace('bg-secondary', 'bg-primary');
                text.classList.add('text-primary', 'fw-bold');
                text.classList.remove('text-muted');
            } else {
                circle.classList.replace('bg-primary', 'bg-secondary');
                text.classList.remove('text-primary', 'fw-bold');
                text.classList.add('text-muted');
            }
        }
    }

    // --- Filter Existing Questions ---
    function filterQuestionsBySubject() {
        let subjectId = document.getElementById('input-subject').value;
        document.getElementById('no-subject-msg').style.display = 'none';
        
        let questions = document.querySelectorAll('.question-item');
        questions.forEach(q => {
            if(q.getAttribute('data-subject') === subjectId) {
                q.style.display = 'block';
            } else {
                q.style.display = 'none';
                q.querySelector('input').checked = false; // uncheck if hidden
            }
        });
    }

    // --- Dynamic Inline Question Creator ---
    function addNewQuestionBox() {
        let container = document.getElementById('new-questions-container');
        let html = `
            <div class="mb-2 position-relative mt-3">
                <textarea name="new_questions[]" class="form-control bg-light border-success" rows="2" placeholder="Type another new question here..."></textarea>
                <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0" style="text-decoration: none;" onclick="this.parentElement.remove()">Remove</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>

@endsection