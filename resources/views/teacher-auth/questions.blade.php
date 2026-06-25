@extends('common.main')

@section('title', 'Question Bank Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        {{-- Sidebar (Keeps the layout uniform) --}}
        <div class="col-lg-2 col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <nav class="nav flex-column nav-pills flex-grow-1">
                        <a href="{{ route('teacherAuth.dashboard') }}"
                           class="nav-link {{ request()->routeIs('teacherAuth.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                        </a>
                        <a href="{{ route('questions.create') }}"
                           class="nav-link {{ request()->routeIs('questions.create*') ? 'active' : '' }}">
                            <i class="bi bi-folder2-open me-2"></i> Question Bank
                        </a>
                        <a href="{{ route('teacher.results.index') }}"
                           class="nav-link {{ request()->routeIs('teacher.results.index*') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-fill me-2"></i> View Results
                        </a>
                    </nav>
                    <form method="POST" action="{{ route('teacherAuth.logout') }}" class="mt-auto m-0">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent text-danger px-3">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Question Workspace Area --}}
        <div class="col-lg-10 col-md-9">
            
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    
                    <div class="mb-4 border-bottom pb-3">
                        <h3 class="fw-bold m-0 text-dark">
                            <i class="bi bi-folder2-open text-primary me-2"></i>Subject Question Bank
                        </h3>
                        <p class="text-muted m-0">Create, update, and manage your localized subject catalogs and multi-choice questions.</p>
                    </div>

                    {{-- Subject Selection Dropdown --}}
                        <form method="GET" action="{{ route('questions.create') }}" id="subjectSelectorForm">
                            <select name="subject_filter" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Choose a Subject Track --</option>
                                @foreach($subjects as $subj)
                                    <option value="{{ $subj->subject_id }}" {{ request('subject_filter') == $subj->subject_id ? 'selected' : '' }}>
                                        {{ $subj->subject_displayname }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        {{-- Action buttons for updating/deleting the subject --}}
                        @if($currentSubject)
                            <div class="mt-3 p-3 bg-light rounded border d-flex justify-content-between align-items-center gap-2">
                                <form action="{{ route('subjects.update', $currentSubject->subject_id) }}" method="POST" class="d-flex gap-2 flex-grow-1">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $currentSubject->subject_displayname }}" required>
                                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">Rename</button>
                                </form>
                                
                                <form action="{{ route('subjects.delete', $currentSubject->subject_id) }}" method="POST" onsubmit="return confirm('Delete this subject permanently?');" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Register New Subject Category</label>
                            <form action="{{ route('subjects.store') }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="name" class="form-control" placeholder="e.g., Computer Science 101" required>
                                    <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-plus-lg"></i> Create</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Question Operations Workspace --}}
                    @if(request('subject_filter') && isset($currentSubject))
                        <hr class="my-4">
                        <div class="row g-4">
                            
                            {{-- Add Question Inline Form Panel --}}
                            <div class="col-lg-4">
                                <div class="p-3 rounded border bg-light">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Add Question to [{{ $currentSubject->subject_displayname }}]</h6>
                                    
                                    <form method="POST" action="{{ route('questions.store') }}">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $currentSubject->subject_id }}">
                                        
                                        <div class="mb-2">
                                            <label class="form-label small text-muted mb-1">Question Text</label>
                                            <textarea class="form-control form-control-sm" name="question_text" rows="3" placeholder="Type query here..." required></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small text-muted mb-1">Choices Options</label>
                                            <div class="d-flex flex-column gap-1">
                                                <input type="text" class="form-control form-control-sm" name="option_a" placeholder="Option A" required>
                                                <input type="text" class="form-control form-control-sm" name="option_b" placeholder="Option B" required>
                                                <input type="text" class="form-control form-control-sm" name="option_c" placeholder="Option C" required>
                                                <input type="text" class="form-control form-control-sm" name="option_d" placeholder="Option D" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted mb-1">Correct Answer Choice</label>
                                            <select class="form-select form-select-sm fw-bold border-success text-success" name="correct_option" required>
                                                <option value="">-- Choose Key --</option>
                                                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Save Question</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Question Pool Records List Grid Table --}}
                            <div class="col-lg-8">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check text-warning me-2"></i>Active Question Pool ({{ $questions->count() }} Items)</h6>
                                
                                <div class="table-responsive border rounded bg-white">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th style="width: 80%;">Question details</th>
                                                <th class="text-center">Key</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            {{-- Check this loop inside resources/views/teacher-auth/questions.blade.php --}}
                                            @forelse($questions as $question)
                                                    <tr>
                                                        <td class="p-3">
                                                            {{-- Corrected text field reference --}}
                                                            <div class="fw-bold text-dark mb-1">{{ $question->quest_desc }}</div>
                                                            
                                                            {{-- Corrected JSON collection indices --}}
                                                            <div class="text-muted row g-1 small">
                                                                <div class="col-6">A: {{ $question->choices['A'] ?? '' }}</div>
                                                                <div class="col-6">B: {{ $question->choices['B'] ?? '' }}</div>
                                                                <div class="col-6">C: {{ $question->choices['C'] ?? '' }}</div>
                                                                <div class="col-6">D: {{ $question->choices['D'] ?? '' }}</div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-success">{{ $question->quest_answer }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <form action="{{ route('questions.delete', $question->question_id) }}" method="POST" onsubmit="return confirm('Remove question?');" class="m-0">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-link p-0 text-danger fs-5"><i class="bi bi-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">
                                                        No questions bound to this repository context yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-primary-subtle text-center border p-4 rounded-3 mt-3 mb-0">
                            <i class="bi bi-hand-index-thumb fs-3 text-primary d-block mb-2"></i>
                            Select an active subject workspace track above to load its associated question lists.
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection