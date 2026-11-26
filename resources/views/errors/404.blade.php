@extends('layouts.app')

@section('title', 'Page not found')

@section('content')
<div class="container">
    <div class="lm-error">
        <div class="code">404</div>
        <h3 class="fw-bold mt-3">We couldn't find that page</h3>
        <p class="text-muted">The ad may have been deleted, or the link is incorrect.</p>
        <a href="{{ route('home') }}" class="btn btn-lm px-4">Back to Home</a>
    </div>
</div>
@endsection
