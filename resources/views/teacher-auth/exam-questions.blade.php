@extends('common.main')
@section('title', 'Manage Exam Questions')
@section('content')

<div class="container-fluid py-4" style="font-family: sans-serif; max-width: 1200px;">
    
    {{-- Top Header Section --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <p class="text-muted small mb-0">Manage Exam Questions</p>
            <h2 class="fw-bold mb-2">{{ $exam->title }}</h2>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary border-opacity-25">
                Access Code: {{ $exam->access_code }}
            </span>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('teacherAuth.dashboard') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                <i class="bi bi-check-circle me-1"></i> Finish & Return
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 p-3 small fw-bold shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 p-3 small shadow-sm">
            <ul class="m-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        
        {{-- Left Column (Actions) --}}
        <div class="col-lg-7 d-flex flex-column gap-4">
            
            {{-- Question Bank Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle me-3" style="width: 45px; height: 45px; font-size: 20px;">
                            <i class="bi bi-database"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Add from Question Bank</h5>
                    </div>
                    
                    <form method="POST" action="{{ route('exams.questions.action', $exam->exam_id) }}">
                        @csrf
                        <div class="p-3 bg-light rounded-4 mb-3" style="max-height: 250px; overflow-y: auto;">
                            @forelse($availableQuestions as $q)
                                <div class="form-check mb-2 bg-white p-3 border-0 shadow-sm rounded-3 d-flex align-items-center transition hover-shadow">
                                    <input class="form-check-input ms-1 me-3 fs-5" type="checkbox" name="questions[]" value="{{ $q->question_id }}" id="q_{{ $q->question_id }}">
                                    <label class="form-check-label small w-100 mb-0" for="q_{{ $q->question_id }}">
                                        {{ $q->quest_desc }}
                                    </label>
                                </div>
                            @empty
                                <div class="text-center text-muted small py-4">No unused questions available for this subject. Create a new one below!</div>
                            @endforelse
                        </div>
                        <button type="submit" name="submit_action" value="add_from_bank" class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2 fw-bold" {{ count($availableQuestions) == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-plus-circle me-1"></i> Add Selected from Bank
                        </button>
                    </form>
                </div>
            </div>

            {{-- Create New Question Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle me-3" style="width: 45px; height: 45px; font-size: 20px;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Create a Brand New Question</h5>
                    </div>

                    <form method="POST" action="{{ route('exams.questions.action', $exam->exam_id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-semibold text-muted mb-1">Question Text</label>
                            <textarea name="question_text" class="form-control bg-light border-0 py-2 rounded-3" rows="3" placeholder="Type new question here..." required></textarea>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <input type="text" name="choice_a" class="form-control bg-light border-0 py-2" placeholder="Option A" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="choice_b" class="form-control bg-light border-0 py-2" placeholder="Option B" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="choice_c" class="form-control bg-light border-0 py-2" placeholder="Option C" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="choice_d" class="form-control bg-light border-0 py-2" placeholder="Option D" required>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-end border-top pt-3">
                            <div class="w-100 w-md-50 mb-3 mb-md-0 me-md-3">
                                <label class="small fw-semibold text-muted mb-1">Correct Answer</label>
                                <select name="correct_answer" class="form-select bg-light border-0 py-2 fw-bold text-primary" required>
                                    <option value="" disabled selected>-- Select Key --</option>
                                    <option value="A">A</option><option value="B">B</option>
                                    <option value="C">C</option><option value="D">D</option>
                                </select>
                            </div>
                            <button type="submit" name="submit_action" value="create_new" class="btn btn-primary rounded-pill py-2 px-4 fw-bold flex-grow-1 flex-md-grow-0">
                                Save & Add to Exam
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- Right Column (Current Exam Items) --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle me-3" style="width: 45px; height: 45px; font-size: 20px;">
                                <i class="bi bi-card-checklist"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Current Exam</h5>
                        </div>
                        <span class="badge bg-primary rounded-pill fs-6">{{ count($attachedQuestions) }} items</span>
                    </div>

                    <div class="flex-grow-1" style="max-height: 700px; overflow-y: auto;">
                        <ul class="list-group list-group-flush gap-2">
                            @forelse($attachedQuestions as $index => $q)
                                <li class="list-group-item bg-light border-0 rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div class="small text-dark">
                                            <span class="text-primary fw-bold me-1">{{ $index + 1 }}.</span> {{ $q->quest_desc }}
                                        </div>
                                        <form method="POST" action="{{ route('exams.questions.action', $exam->exam_id) }}">
                                            @csrf
                                            <input type="hidden" name="remove_id" value="{{ $q->question_id }}">
                                            <button type="submit" name="submit_action" value="remove_question" class="btn btn-link text-danger text-opacity-75 p-0 text-decoration-none small fw-bold" title="Remove Question">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <div class="text-center text-muted small py-5 mt-4">
                                    <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 70px; height: 70px; font-size: 30px;">
                                        <i class="bi bi-file-earmark-x"></i>
                                    </div>
                                    <p class="mb-0">No questions added yet.</p>
                                    <p>Use the tools on the left to build your exam!</p>
                                </div>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .transition { transition: all 0.2s ease; }
    .hover-shadow:hover { box-shadow: 0 .125rem .25rem rgba(111, 66, 193, 0.15)!important; transform: translateY(-2px); }
</style>
@endsection