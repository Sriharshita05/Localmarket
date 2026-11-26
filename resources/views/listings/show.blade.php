@extends('layouts.app')

@section('title', $listing->name)

@section('content')
<div class="container">

    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('category.show', ['category' => $listing->category->slug]) }}">{{ $listing->category->name }}</a>
            </li>
            @if ($listing->subcategory)
                <li class="breadcrumb-item">
                    <a href="{{ route('category.show', ['category' => $listing->category->slug, 'sub' => $listing->subcategory->slug]) }}">
                        {{ $listing->subcategory->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($listing->name, 40) }}</li>
        </ol>
    </nav>

    <div class="row g-4">

        {{-- Main details --}}
        <div class="col-lg-8">
            <div class="card lm-card overflow-hidden">
                <div class="lm-detail-banner">
                    <i class="fa-solid {{ $listing->category->icon ?? 'fa-tag' }}"></i>
                    @if ($listing->image_src)
                        <img src="{{ $listing->image_src }}" alt="{{ $listing->name }}"
                             referrerpolicy="no-referrer" onerror="this.remove()">
                    @endif
                </div>

                <div class="p-4">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge {{ $listing->type === 'service' ? 'bg-warning text-dark' : 'bg-primary' }}">
                            {{ ucfirst($listing->type) }}
                        </span>
                        <span class="badge lm-cat-badge">{{ $listing->category->name }}</span>
                        @if ($listing->subcategory)
                            <span class="badge lm-cat-badge">{{ $listing->subcategory->name }}</span>
                        @endif
                    </div>

                    <h2 class="fw-bold mb-1">{{ $listing->name }}</h2>
                    <div class="lm-detail-price mb-3">₹{{ number_format($listing->price, 2) }}</div>

                    <h5 class="fw-bold">Description</h5>
                    <p class="lm-description">{{ $listing->description }}</p>

                    <hr>

                    <h5 class="fw-bold mb-3">Location</h5>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="lm-detail-label">Country</div>
                            <div class="fw-semibold">{{ $listing->country }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="lm-detail-label">State</div>
                            <div class="fw-semibold">{{ $listing->state }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="lm-detail-label">City</div>
                            <div class="fw-semibold">
                                <a href="{{ route('city.show', ['city' => $listing->city_slug]) }}" class="text-decoration-none">
                                    {{ $listing->city }}
                                </a>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="lm-detail-label">Area</div>
                            <div class="fw-semibold">{{ $listing->area }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card lm-card p-4 mb-3">
                <h6 class="text-muted text-uppercase small mb-3">Seller</h6>
                <div class="d-flex align-items-center gap-3">
                    <div class="lm-avatar">{{ strtoupper(substr($listing->user->name, 0, 1)) }}</div>
                    <div>
                        <div class="fw-bold">{{ $listing->user->name }}</div>
                        <small class="text-muted">Member since {{ $listing->user->created_at->format('M Y') }}</small>
                    </div>
                </div>

                <hr>

                <div class="small text-muted mb-1"><i class="fa-regular fa-calendar"></i> Posted on</div>
                <div class="fw-semibold mb-3">{{ $listing->created_at->format('d M Y') }}
                    <span class="text-muted fw-normal">({{ $listing->created_at->diffForHumans() }})</span>
                </div>

                <div class="small text-muted mb-1"><i class="fa-solid fa-location-dot"></i> Location</div>
                <div class="fw-semibold">{{ $listing->area }}, {{ $listing->city }}, {{ $listing->state }}</div>
            </div>

            {{-- Only the owner sees these --}}
            @can('update', $listing)
                <div class="card lm-card p-4 mb-3">
                    <h6 class="text-muted text-uppercase small mb-3">Manage this ad</h6>
                    <a href="{{ route('listings.edit', $listing) }}" class="btn btn-lm w-100 mb-2">
                        <i class="fa-solid fa-pen"></i> Edit Ad
                    </a>
                    <form action="{{ route('listings.destroy', $listing) }}" method="POST"
                          onsubmit="return confirm('Delete this ad permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fa-solid fa-trash"></i> Delete Ad
                        </button>
                    </form>
                </div>
            @endcan

            <a href="{{ route('city.category', ['city' => $listing->city_slug, 'category' => $listing->category->slug]) }}"
               class="btn btn-outline-lm w-100">
                More {{ $listing->category->name }} in {{ $listing->city }}
            </a>
        </div>
    </div>

    {{-- Related listings --}}
    @if ($related->count())
        <section class="mt-5">
            <h4 class="fw-bold mb-3">More in {{ $listing->category->name }}</h4>
            <div class="row g-3">
                @foreach ($related as $item)
                    @include('partials.listing-card', ['listing' => $item])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection