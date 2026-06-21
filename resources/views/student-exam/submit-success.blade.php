@extends('common.main')
@section('title', 'Submission Successful')
@section('content')

<div class="container py-5 text-center" style="font-family: sans-serif;">
    <div class="card mx-auto p-4" style="max-width: 600px;">
        <h2 class="text-success">Exam Successfully Submitted!</h2>
        <p>Your answers for <strong>{{ $exam->title }}</strong> have been recorded.</p>
        <a href="{{ route('studentExam.list') }}" class="btn btn-primary mt-3">Back to Exam List</a>
        <a href="{{ route('studentAuth.dashboard') }}" class="btn btn-outline-secondary mt-2">Back to Dashboard</a>
    </div>
</div>
@endsection