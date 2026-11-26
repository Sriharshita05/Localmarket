<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        // Main categories only (parent_id is null)
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();

        // Cities that have listings, most active first
        $cities = Listing::selectRaw('city_slug, MIN(city) as city, COUNT(*) as total')
            ->groupBy('city_slug')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $listings = Listing::with(['category', 'subcategory', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('area', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('home', compact('categories', 'cities', 'listings', 'search'));
    }
}