@extends('layouts.app')

@section('title', 'Buy & Sell Locally')

@section('content')

{{-- Hero --}}
<section class="lm-hero mb-5">
    <div class="container text-center">
        <h1 class="fw-bold">Buy, sell and find services <span>near you</span></h1>
        <p class="mb-4">Thousands of local products and services, all in one place.</p>
        <a href="{{ route('listings.create') }}" class="btn btn-light btn-lg fw-semibold px-4">
            <i class="fa-solid fa-plus"></i> Post Your Ad
        </a>
    </div>
</section>

<div class="container">

    @if ($search === '')

        {{-- Categories --}}
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Browse Categories</h4>
                <a href="{{ route('categories.index') }}" class="fw-semibold text-decoration-none">View all</a>
            </div>

            <div class="row g-3">
                @foreach ($categories as $category)
                    <div class="col-6 col-md-3 col-lg-3">
                        <a href="{{ route('category.show', ['category' => $category->slug]) }}" class="text-decoration-none">
                            <div class="lm-category-tile text-center">
                                <div class="lm-category-icon">
                                    <i class="fa-solid {{ $category->icon ?? 'fa-tag' }}"></i>
                                </div>
                                <div class="fw-semibold">{{ $category->name }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Browse by City --}}
        @if ($cities->count())
            <section class="mb-5">
                <h4 class="fw-bold mb-3">Browse by City</h4>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($cities as $c)
                        <a href="{{ route('city.show', ['city' => $c->city_slug]) }}" class="lm-pill">
                            <i class="fa-solid fa-location-dot"></i> {{ $c->city }} <small>({{ $c->total }})</small>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    @endif

    {{-- Listings --}}
    <section>
        <h4 class="fw-bold mb-3">
            @if ($search !== '')
                Results for "{{ $search }}"
            @else
                Latest Listings
            @endif
        </h4>

        @if ($listings->count())
            <div class="row g-3">
                @foreach ($listings as $listing)
                    @include('partials.listing-card', ['listing' => $listing])
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $listings->links() }}
            </div>
        @else
            <div class="lm-empty text-center">
                <i class="fa-regular fa-folder-open"></i>
                <h5 class="fw-bold mt-3">No listings found</h5>
                <p class="text-muted mb-3">
                    @if ($search !== '')
                        Nothing matched your search. Try different keywords.
                    @else
                        Nobody has posted an ad yet. Be the first!
                    @endif
                </p>
                <a href="{{ route('listings.create') }}" class="btn btn-lm px-4">Post Your Ad</a>
            </div>
        @endif
    </section>
</div>

@endsection