<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Listing extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'subcategory_id', 'type', 'name', 'description',
        'country', 'state', 'city', 'city_slug', 'area', 'price', 'image_url', 'image_path',
    ];

    protected static function booted(): void
    {
        // Automatically keep city_slug in sync with city
        static::saving(function (Listing $listing) {
            $listing->city_slug = Str::slug($listing->city);
        });

        // Remove the uploaded file when the listing is deleted
        static::deleting(function (Listing $listing) {
            if ($listing->image_path) {
                Storage::disk('public')->delete($listing->image_path);
            }
        });
    }

    // Uploaded photo wins; otherwise the pasted image link (or null)
    public function getImageSrcAttribute(): ?string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        return $this->image_url;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }
}