@extends('layouts.app')

@section('title', 'Edit Ad')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card lm-card p-4">
                <h3 class="fw-bold mb-1">Edit your ad</h3>
                <p class="text-muted mb-4">Update the details and save your changes.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> Please fix the errors below and try again.
                    </div>
                @endif

                <form action="{{ route('listings.update', $listing) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('listings._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-lm px-4 py-2">Save Changes</button>
                        <a href="{{ route('listings.my') }}" class="btn btn-light px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection