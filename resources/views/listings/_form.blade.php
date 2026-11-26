@php
    $subMap = $categories->mapWithKeys(fn ($c) => [
        $c->id => $c->subcategories->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values(),
    ]);
@endphp

<div class="row g-3">

    {{-- Type --}}
    <div class="col-12">
        <label class="form-label d-block fw-semibold">Listing type</label>
        @foreach (['product' => 'Product', 'service' => 'Service'] as $value => $label)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="type" id="type_{{ $value }}"
                       value="{{ $value }}" {{ old('type', $listing->type ?: 'product') === $value ? 'checked' : '' }}>
                <label class="form-check-label" for="type_{{ $value }}">{{ $label }}</label>
            </div>
        @endforeach
        @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    {{-- Name --}}
    <div class="col-12">
        <label for="name" class="form-label fw-semibold">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $listing->name) }}"
               class="form-control @error('name') is-invalid @enderror" placeholder="e.g. iPhone 13 128GB or Home Cleaning Service">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Description --}}
    <div class="col-12">
        <label for="description" class="form-label fw-semibold">Detail / Description</label>
        <textarea id="description" name="description" rows="5"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Describe condition, features, availability...">{{ old('description', $listing->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Category --}}
    <div class="col-md-6">
        <label for="category_id" class="form-label fw-semibold">Category</label>
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ (string) old('category_id', $listing->category_id) === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Subcategory (filled by JavaScript) --}}
    <div class="col-md-6">
        <label for="subcategory_id" class="form-label fw-semibold">Subcategory</label>
        <select id="subcategory_id" name="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror">
            <option value="">Select subcategory</option>
        </select>
        @error('subcategory_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Location --}}
    <div class="col-md-6">
        <label for="country" class="form-label fw-semibold">Country</label>
        <input type="text" id="country" name="country" value="{{ old('country', $listing->country ?: 'India') }}"
               class="form-control @error('country') is-invalid @enderror">
        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="state" class="form-label fw-semibold">State</label>
        <input type="text" id="state" name="state" value="{{ old('state', $listing->state) }}"
               class="form-control @error('state') is-invalid @enderror" placeholder="e.g. Punjab">
        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="city" class="form-label fw-semibold">City</label>
        <input type="text" id="city" name="city" value="{{ old('city', $listing->city) }}"
               class="form-control @error('city') is-invalid @enderror" placeholder="e.g. Jalandhar">
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="area" class="form-label fw-semibold">Area</label>
        <input type="text" id="area" name="area" value="{{ old('area', $listing->area) }}"
               class="form-control @error('area') is-invalid @enderror" placeholder="e.g. Model Town">
        @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Price --}}
    <div class="col-md-6">
        <label for="price" class="form-label fw-semibold">Price (₹)</label>
        <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $listing->price) }}"
               class="form-control @error('price') is-invalid @enderror">
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Photo --}}
    <div class="col-12">
        <label for="image" class="form-label fw-semibold">Photo <span class="text-muted fw-normal">(optional)</span></label>

        @if ($listing->image_src)
            <div class="mb-2 d-flex align-items-center gap-3">
                <img src="{{ $listing->image_src }}" alt="Current photo" class="rounded border"
                     style="width:90px;height:90px;object-fit:cover;" onerror="this.remove()">
                @if ($listing->image_path)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">Remove uploaded photo</label>
                    </div>
                @endif
            </div>
        @endif

        <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp"
               class="form-control @error('image') is-invalid @enderror">
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">JPG, PNG or WEBP, up to 2 MB.</div>
    </div>

    <div class="col-12">
        <label for="image_url" class="form-label fw-semibold">Or use an image link <span class="text-muted fw-normal">(optional)</span></label>
        <input type="url" id="image_url" name="image_url" value="{{ old('image_url', $listing->image_url) }}"
               class="form-control @error('image_url') is-invalid @enderror"
               placeholder="https://example.com/photo.jpg">
        @error('image_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">
            On Google Images, open a picture, right-click the large image and choose "Copy image address". An uploaded photo takes priority over the link.
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const subMap = @json($subMap);
    let selectedSub = @json((string) old('subcategory_id', $listing->subcategory_id));
    const catSelect = document.getElementById('category_id');
    const subSelect = document.getElementById('subcategory_id');

    function loadSubcategories() {
        const subs = subMap[catSelect.value] || [];
        subSelect.innerHTML = '<option value="">Select subcategory</option>';
        subs.forEach(function (s) {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.name;
            if (String(s.id) === selectedSub) opt.selected = true;
            subSelect.appendChild(opt);
        });
    }

    catSelect.addEventListener('change', function () {
        selectedSub = '';
        loadSubcategories();
    });

    loadSubcategories();
});
</script>