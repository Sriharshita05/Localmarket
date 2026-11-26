<div class="col-6 col-md-4 col-lg-3">
    <a href="{{ route('listings.show', $listing) }}" class="text-decoration-none">
        <div class="card lm-listing-card h-100">
            <div class="lm-listing-thumb">
                <i class="fa-solid {{ $listing->category->icon ?? 'fa-tag' }}"></i>
                @if ($listing->image_src)
                    <img src="{{ $listing->image_src }}" alt="{{ $listing->name }}"
                         loading="lazy" referrerpolicy="no-referrer" onerror="this.remove()">
                @endif
                <span class="badge lm-type-badge {{ $listing->type === 'service' ? 'bg-warning text-dark' : 'bg-primary' }}">
                    {{ ucfirst($listing->type) }}
                </span>
            </div>
            <div class="card-body">
                <div class="lm-price">₹{{ number_format($listing->price, 0) }}</div>
                <h6 class="lm-listing-title">{{ Str::limit($listing->name, 45) }}</h6>
                <div class="small text-muted mb-1">
                    <i class="fa-solid fa-location-dot"></i> {{ $listing->city }}
                </div>
                <span class="badge lm-cat-badge">{{ $listing->category->name }}</span>
            </div>
        </div>
    </a>
</div>