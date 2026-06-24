@extends('common.main')
@section('title', 'Add New Question')
@section('content')

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 bg-white p-4 rounded-3">
            <h5 class="fw-bold text-secondary mb-3">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add New Question
            </h5>
            
            <form method="POST" action="{{ route('questions.store') }}">
                @csrf
                
                @if($errors->any())
                    <div class="alert alert-danger p-2 small">
                        <ul class="m-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if(session('success'))
                    <div class="alert alert-success p-2 small">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Select Subject</label>
                    <select class="form-select form-select-sm" name="subject_id" required>
                        <option value="">-- Choose Subject --</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->subject_id }}">{{ $subj->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Question Text</label>
                    <textarea class="form-control form-control-sm" name="question_text" rows="2" placeholder="Type question here..." required></textarea>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6"><input type="text" class="form-control form-control-sm" name="choice_a" placeholder="Option A" required></div>
                    <div class="col-6"><input type="text" class="form-control form-control-sm" name="choice_b" placeholder="Option B" required></div>
                    <div class="col-6"><input type="text" class="form-control form-control-sm" name="choice_c" placeholder="Option C" required></div>
                    <div class="col-6"><input type="text" class="form-control form-control-sm" name="choice_d" placeholder="Option D" required></div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Correct Option Key</label>
                    <select class="form-select form-select-sm border-success text-success fw-bold" name="correct_answer" required>
                        <option value="">-- Select Answer Key --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <button type="submit" name="submit_action" value="add_another" class="btn btn-primary btn-sm d-grid w-100 fw-bold">
                    Save to Question Bank
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0 bg-white p-4 rounded-3">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="fw-bold text-secondary m-0">
                    <i class="bi bi-database-fill me-2 text-warning"></i>Question Bank Repository
                </h4>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle border-top m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject ID</th>
                            <th>Question Summary</th>
                            <th class="text-center">Key</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted small">
                                Add a question on the left to populate the bank.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection