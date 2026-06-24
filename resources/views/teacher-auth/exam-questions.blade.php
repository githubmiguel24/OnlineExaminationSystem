@extends('common.main')
@section('title', 'Manage Exam Questions')
@section('content')

<div class="container-fluid py-4" style="font-family: sans-serif; max-width: 1200px;">
    
    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
        <div>
            <h3 class="text-muted m-0 mt-1">Exam Name: <strong>{{ $exam->title }}</strong> </h3>
            <h5>(Code: <span class="badge bg-dark">{{ $exam->access_code }}</span>)</h5>
        </div>
        <a href="{{ route('teacherAuth.dashboard') }}" class="btn btn-success fw-bold px-4 rounded-pill">Finish & Return to Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success p-2 small fw-bold"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger p-2 small">
            <ul class="m-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        
        <div class="col-md-7 d-flex flex-column gap-4">
            
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white fw-bold text-secondary border-bottom">
                    <i class="bi bi-database me-2"></i>Add from Question Bank
                </div>
                <div class="card-body p-0">
                    <form method="POST" action="{{ route('exams.questions.action', $exam->exam_id) }}">
                        @csrf
                        <div class="p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                            @forelse($availableQuestions as $q)
                                <div class="form-check mb-2 bg-white p-2 border rounded">
                                    <input class="form-check-input ms-1" type="checkbox" name="questions[]" value="{{ $q->question_id }}" id="q_{{ $q->question_id }}">
                                    <label class="form-check-label ms-2 small w-100" for="q_{{ $q->question_id }}">
                                        {{ $q->quest_desc }}
                                    </label>
                                </div>
                            @empty
                                <div class="text-center text-muted small py-3">No unused questions available for this subject. Create a new one below!</div>
                            @endforelse
                        </div>
                        <div class="p-3 border-top">
                            <button type="submit" name="submit_action" value="add_from_bank" class="btn btn-secondary btn-sm w-100 fw-bold" {{ count($availableQuestions) == 0 ? 'disabled' : '' }}>
                                + Add Selected from Bank
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-primary rounded-3">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Or Create a Brand New Question
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('exams.questions.action', $exam->exam_id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold text-primary">Question Text</label>
                            <textarea name="question_text" class="form-control bg-light border-0" rows="2" placeholder="Type new question here..." required></textarea>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><input type="text" name="choice_a" class="form-control form-control-sm bg-light border-0" placeholder="Option A" required></div>
                            <div class="col-6"><input type="text" name="choice_b" class="form-control form-control-sm bg-light border-0" placeholder="Option B" required></div>
                            <div class="col-6"><input type="text" name="choice_c" class="form-control form-control-sm bg-light border-0" placeholder="Option C" required></div>
                            <div class="col-6"><input type="text" name="choice_d" class="form-control form-control-sm bg-light border-0" placeholder="Option D" required></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="w-50">
                                <label class="small fw-bold text-primary">Correct Answer</label>
                                <select name="correct_answer" class="form-select form-select-sm border-primary text-primary fw-bold" required>
                                    <option value="" disabled selected>-- Select Key --</option>
                                    <option value="A">A</option><option value="B">B</option>
                                    <option value="C">C</option><option value="D">D</option>
                                </select>
                            </div>
                            <button type="submit" name="submit_action" value="create_new" class="btn btn-primary mt-4 fw-bold px-4">
                                Save & Add to Exam
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="col-md-5">
            <div class="card shadow-sm border-success h-100 rounded-3">
                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-check2-square me-2"></i>Currently on Exam</span>
                    <span class="badge bg-white text-success">{{ count($attachedQuestions) }} items</span>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        @forelse($attachedQuestions as $index => $q)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="small fw-semibold me-3">
                                        <span class="text-success me-1">{{ $index + 1 }}.</span> {{ $q->quest_desc }}
                                    </div>
                                    
                                    <form method="POST" action="{{ route('exams.questions.action', $exam->exam_id) }}">
                                        @csrf
                                        <input type="hidden" name="remove_id" value="{{ $q->question_id }}">
                                        <button type="submit" name="submit_action" value="remove_question" class="btn btn-link text-danger p-0 m-0 text-decoration-none small fw-bold">Remove</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted small py-5">
                                <h1 class="mb-3 text-light">📄</h1>
                                No questions added yet.<br>Use the tools on the left to build your exam!
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection