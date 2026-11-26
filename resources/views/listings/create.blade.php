@extends('layouts.app')

@section('title', 'Post Your Ad')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card lm-card p-4">
                <h3 class="fw-bold mb-1">Post your ad</h3>
                <p class="text-muted mb-4">Fill in the details below to list a product or service.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> Please fix the errors below and try again.
                    </div>
                @endif

                <form action="{{ route('listings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('listings._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-lm px-4 py-2">Post Ad</button>
                        <a href="{{ route('home') }}" class="btn btn-light px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection