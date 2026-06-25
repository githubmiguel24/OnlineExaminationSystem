@extends('common.main')
@section('title', 'Available Exams')
@section('content')

<div class="container py-5" style="font-family: sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <a href="{{ route('studentAuth.dashboard') }}" class="btn btn-sm btn-outline-primary">Dashboard</a>
            <form method="POST" action="{{ route('studentAuth.logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

</div>
@endsection