<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    // Only the owner can update their listing
    public function update(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    // Only the owner can delete their listing
    public function delete(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }
}