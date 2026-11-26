<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    // Display name for a city slug, e.g. "jalandhar" => "Jalandhar"
    private function cityName(string $slug): string
    {
        return Listing::where('city_slug', $slug)->value('city') ?? Str::headline($slug);
    }

    // /city/jalandhar
    public function show(string $city)
    {
        $city = Str::slug($city);
        $cityName = $this->cityName($city);

        $listings = Listing::with(['category', 'subcategory'])
            ->where('city_slug', $city)
            ->latest()
            ->paginate(12);

        // Only categories that have listings in this city, with counts
        $categories = Category::whereNull('parent_id')
            ->withCount(['listings' => fn ($q) => $q->where('city_slug', $city)])
            ->orderBy('name')
            ->get()
            ->filter(fn ($c) => $c->listings_count > 0);

        $pills = [[
            'label' => 'All',
            'url' => route('city.show', ['city' => $city]),
            'active' => true,
        ]];

        foreach ($categories as $c) {
            $pills[] = [
                'label' => $c->name . ' (' . $c->listings_count . ')',
                'url' => route('city.category', ['city' => $city, 'category' => $c->slug]),
                'active' => false,
            ];
        }

        return view('browse', [
            'title' => 'Listings in ' . $cityName,
            'heading' => 'Listings in ' . $cityName,
            'subtitle' => $listings->total() . ' product(s) and service(s) available',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $cityName, 'url' => null],
            ],
            'pills' => $pills,
            'listings' => $listings,
            'emptyMessage' => 'No listings in ' . $cityName . ' yet.',
        ]);
    }

    // /city/jalandhar/category/electronics
    public function showCategory(Request $request, string $city, Category $category)
    {
        $city = Str::slug($city);

        if ($category->parent_id) {
            return redirect()->route('city.category', [
                'city' => $city,
                'category' => $category->parent->slug,
                'sub' => $category->slug,
            ]);
        }

        $cityName = $this->cityName($city);
        $subcategories = $category->subcategories()->orderBy('name')->get();
        $activeSub = $subcategories->firstWhere('slug', $request->query('sub'));

        $listings = Listing::with(['category', 'subcategory'])
            ->where('city_slug', $city)
            ->where('category_id', $category->id)
            ->when($activeSub, fn ($q) => $q->where('subcategory_id', $activeSub->id))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $pills = [[
            'label' => 'All ' . $category->name,
            'url' => route('city.category', ['city' => $city, 'category' => $category->slug]),
            'active' => ! $activeSub,
        ]];

        foreach ($subcategories as $sub) {
            $pills[] = [
                'label' => $sub->name,
                'url' => route('city.category', ['city' => $city, 'category' => $category->slug, 'sub' => $sub->slug]),
                'active' => $activeSub && $activeSub->id === $sub->id,
            ];
        }

        return view('browse', [
            'title' => $category->name . ' in ' . $cityName,
            'heading' => $category->name . ' in ' . $cityName,
            'subtitle' => $listings->total() . ' listing(s) found',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $cityName, 'url' => route('city.show', ['city' => $city])],
                ['label' => $category->name, 'url' => null],
            ],
            'pills' => $pills,
            'listings' => $listings,
            'emptyMessage' => 'No ' . $category->name . ' listings in ' . $cityName . ' yet.',
        ]);
    }
}