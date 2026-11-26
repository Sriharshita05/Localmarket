<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // All main categories with their subcategories
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('subcategories')
            ->withCount('listings')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    // Listings of one category, optionally filtered by ?sub=slug
    public function show(Request $request, Category $category)
    {
        // If someone opens /category/mobiles (a subcategory), send them to its parent with the filter applied
        if ($category->parent_id) {
            return redirect()->route('category.show', [
                'category' => $category->parent->slug,
                'sub' => $category->slug,
            ]);
        }

        $subcategories = $category->subcategories()->orderBy('name')->get();
        $activeSub = $subcategories->firstWhere('slug', $request->query('sub'));

        $listings = Listing::with(['category', 'subcategory'])
            ->where('category_id', $category->id)
            ->when($activeSub, fn ($q) => $q->where('subcategory_id', $activeSub->id))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $pills = [[
            'label' => 'All',
            'url' => route('category.show', ['category' => $category->slug]),
            'active' => ! $activeSub,
        ]];

        foreach ($subcategories as $sub) {
            $pills[] = [
                'label' => $sub->name,
                'url' => route('category.show', ['category' => $category->slug, 'sub' => $sub->slug]),
                'active' => $activeSub && $activeSub->id === $sub->id,
            ];
        }

        return view('browse', [
            'title' => $category->name,
            'heading' => $activeSub ? $category->name . ' › ' . $activeSub->name : $category->name,
            'subtitle' => $listings->total() . ' listing(s) found',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => 'Categories', 'url' => route('categories.index')],
                ['label' => $category->name, 'url' => null],
            ],
            'pills' => $pills,
            'listings' => $listings,
            'emptyMessage' => 'No listings in this category yet.',
        ]);
    }
}