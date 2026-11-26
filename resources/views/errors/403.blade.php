@extends('layouts.app')

@section('title', 'Not allowed')

@section('content')
<div class="container">
    <div class="lm-error">
        <div class="code">403</div>
        <h3 class="fw-bold mt-3">You can't do that</h3>
        <p class="text-muted">You can only edit or delete your own ads.</p>
        <a href="{{ route('home') }}" class="btn btn-lm px-4">Back to Home</a>
    </div>
</div>
@endsection
