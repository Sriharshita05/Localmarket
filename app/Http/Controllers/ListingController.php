<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // Shared validation rules for create and update
    private function rules(Request $request): array
    {
        return [
            'type' => ['required', Rule::in(['product', 'service'])],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            // must be a main category (parent_id is null)
            'category_id' => ['required', Rule::exists('categories', 'id')->whereNull('parent_id')],
            // must be a child of the chosen main category
            'subcategory_id' => [
                'required',
                Rule::exists('categories', 'id')->where('parent_id', $request->input('category_id')),
            ],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    // Saves an uploaded photo (or removes it) and strips non-database fields
    private function handleImage(Request $request, array $data, ?Listing $listing = null): array
    {
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($listing?->image_path) {
                Storage::disk('public')->delete($listing->image_path);
            }
            $data['image_path'] = $request->file('image')->store('listings', 'public');
        } elseif ($request->boolean('remove_image') && $listing?->image_path) {
            Storage::disk('public')->delete($listing->image_path);
            $data['image_path'] = null;
        }

        return $data;
    }

    private function mainCategories()
    {
        return Category::whereNull('parent_id')->with('subcategories')->orderBy('name')->get();
    }
        // Public details page
    public function show(Listing $listing)
    {
        $listing->load(['category', 'subcategory', 'user']);

        // More listings from the same category, excluding this one
        $related = Listing::with(['category', 'subcategory'])
            ->where('category_id', $listing->category_id)
            ->where('id', '!=', $listing->id)
            ->latest()
            ->limit(4)
            ->get();

        return view('listings.show', compact('listing', 'related'));
    }
    // "My Ads" page: only the logged-in user's listings
    public function myAds(Request $request)
    {
        $listings = $request->user()
            ->listings()
            ->with(['category', 'subcategory'])
            ->latest()
            ->paginate(10);

        return view('listings.my', compact('listings'));
    }

    public function create()
    {
        return view('listings.create', [
            'listing' => new Listing(),
            'categories' => $this->mainCategories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request));

        $data = $this->handleImage($request, $data);

        // Creating through the relationship sets user_id automatically
        $request->user()->listings()->create($data);

        return redirect()->route('listings.my')
            ->with('success', 'Your ad has been posted successfully!');
    }

    public function edit(Listing $listing)
    {
        Gate::authorize('update', $listing);

        return view('listings.edit', [
            'listing' => $listing,
            'categories' => $this->mainCategories(),
        ]);
    }

    public function update(Request $request, Listing $listing)
    {
        Gate::authorize('update', $listing);

        $data = $request->validate($this->rules($request));
        $data = $this->handleImage($request, $data, $listing);
        $listing->update($data);

        return redirect()->route('listings.my')
            ->with('success', 'Your ad has been updated.');
    }

    public function destroy(Listing $listing)
    {
        Gate::authorize('delete', $listing);

        $listing->delete();

        return redirect()->route('listings.my')
            ->with('success', 'Your ad has been deleted.');
    }
}