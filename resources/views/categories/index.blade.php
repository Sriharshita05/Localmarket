@extends('layouts.app')

@section('title', 'All Categories')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <h3 class="fw-bold mb-4">All Categories</h3>

    <div class="row g-3">
        @foreach ($categories as $category)
            <div class="col-md-6 col-lg-4">
                <div class="card lm-card h-100 p-3">
                    <a href="{{ route('category.show', ['category' => $category->slug]) }}"
                       class="d-flex align-items-center gap-3 text-decoration-none mb-3">
                        <div class="lm-category-icon m-0">
                            <i class="fa-solid {{ $category->icon ?? 'fa-tag' }}"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $category->name }}</div>
                            <small class="text-muted">{{ $category->listings_count }} listing(s)</small>
                        </div>
                    </a>

                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($category->subcategories as $sub)
                            <a href="{{ route('category.show', ['category' => $category->slug, 'sub' => $sub->slug]) }}"
                               class="lm-pill">{{ $sub->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection