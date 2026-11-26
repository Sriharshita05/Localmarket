@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container">

    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $crumb)
                @if ($crumb['url'])
                    <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>

    {{-- Heading --}}
    <div class="mb-3">
        <h3 class="fw-bold mb-1">{{ $heading }}</h3>
        <p class="text-muted mb-0">{{ $subtitle }}</p>
    </div>

    {{-- Filter pills --}}
    @if (count($pills) > 1)
        <div class="d-flex flex-wrap gap-2 mb-4">
            @foreach ($pills as $pill)
                <a href="{{ $pill['url'] }}" class="lm-pill {{ $pill['active'] ? 'active' : '' }}">
                    {{ $pill['label'] }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Listings --}}
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
            <h5 class="fw-bold mt-3">Nothing here yet</h5>
            <p class="text-muted mb-3">{{ $emptyMessage }}</p>
            <a href="{{ route('listings.create') }}" class="btn btn-lm px-4">Post Your Ad</a>
        </div>
    @endif
</div>
@endsection