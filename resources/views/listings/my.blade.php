@extends('layouts.app')

@section('title', 'My Ads')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">My Ads</h4>
        <a href="{{ route('listings.create') }}" class="btn btn-lm"><i class="fa-solid fa-plus"></i> Post New Ad</a>
    </div>

    @if ($listings->count())
        <div class="card lm-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>City</th>
                            <th>Price</th>
                            <th>Posted</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listings as $listing)
                            <tr>
                                <td>
                                    <a href="{{ route('listings.show', $listing) }}" class="fw-semibold text-decoration-none">
                                        {{ $listing->name }}
                                    </a>
                                    <span class="badge bg-light text-dark border ms-1">{{ ucfirst($listing->type) }}</span>
                                </td>
                                <td>{{ $listing->category->name }} / {{ $listing->subcategory->name ?? '-' }}</td>
                                <td>{{ $listing->city }}</td>
                                <td>₹{{ number_format($listing->price, 0) }}</td>
                                <td>{{ $listing->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('listings.edit', $listing) }}" class="btn btn-sm btn-outline-lm">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('listings.destroy', $listing) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this ad permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $listings->links() }}
        </div>
    @else
        <div class="lm-empty text-center">
            <i class="fa-regular fa-folder-open"></i>
            <h5 class="fw-bold mt-3">You haven't posted any ads yet</h5>
            <p class="text-muted mb-3">Post your first ad and reach buyers in your city.</p>
            <a href="{{ route('listings.create') }}" class="btn btn-lm px-4">Post Your Ad</a>
        </div>
    @endif
</div>
@endsection