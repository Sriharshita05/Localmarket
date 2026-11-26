<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocalMarketTest extends TestCase
{
    use RefreshDatabase;

    private function makeListing(User $user, array $overrides = []): Listing
    {
        $category = Category::where('slug', 'electronics')->first();
        $sub = Category::where('slug', 'laptops')->first();

        return Listing::create(array_merge([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subcategory_id' => $sub->id,
            'type' => 'product',
            'name' => 'Test Laptop',
            'description' => 'A laptop in good condition for testing.',
            'country' => 'India',
            'state' => 'Punjab',
            'city' => 'Jalandhar',
            'area' => 'Model Town',
            'price' => 25000,
        ], $overrides));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_homepage_shows_empty_state_then_listing(): void
    {
        $this->get('/')->assertOk()->assertSee('No listings found');

        $user = User::factory()->create();
        $this->makeListing($user);

        $this->get('/')->assertOk()->assertSee('Test Laptop')->assertSee('Jalandhar');
    }

    public function test_registration_hashes_password_and_logs_in(): void
    {
        $this->post('/register', [
            'name' => 'Asha',
            'email' => 'asha@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertNotSame('secret123', User::first()->password);
    }

    public function test_guest_cannot_open_create_form(): void
    {
        $this->get('/listings/create')->assertRedirect('/login');
    }

    public function test_user_can_create_listing_and_it_belongs_to_them(): void
    {
        $user = User::factory()->create();
        $cat = Category::where('slug', 'electronics')->first();
        $sub = Category::where('slug', 'mobiles')->first();

        $this->actingAs($user)->post('/listings', [
            'type' => 'product',
            'name' => 'iPhone 13',
            'description' => 'Very good condition phone.',
            'category_id' => $cat->id,
            'subcategory_id' => $sub->id,
            'country' => 'India',
            'state' => 'Punjab',
            'city' => 'Jalandhar',
            'area' => 'Model Town',
            'price' => 35000,
            'image_url' => 'https://example.com/phone.jpg',
        ])->assertRedirect(route('listings.my'));

        $this->assertDatabaseHas('listings', [
            'name' => 'iPhone 13',
            'user_id' => $user->id,
            'city_slug' => 'jalandhar',
        ]);
    }

    public function test_validation_rejects_bad_input_and_mismatched_subcategory(): void
    {
        $user = User::factory()->create();
        $cat = Category::where('slug', 'electronics')->first();
        $wrongSub = Category::where('slug', 'cars')->first(); // belongs to Vehicles

        $this->actingAs($user)->post('/listings', [
            'type' => 'product',
            'name' => 'x',
            'description' => 'Long enough description.',
            'category_id' => $cat->id,
            'subcategory_id' => $wrongSub->id,
            'country' => 'India',
            'state' => 'Punjab',
            'city' => 'Jalandhar',
            'area' => 'Model Town',
            'price' => 100,
            'image_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors(['subcategory_id', 'image_url']);

        $this->assertDatabaseCount('listings', 0);
    }

    public function test_other_user_cannot_edit_or_delete_listing(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $listing = $this->makeListing($owner);

        $this->actingAs($intruder)->get("/listings/{$listing->id}/edit")->assertForbidden();
        $this->actingAs($intruder)->delete("/listings/{$listing->id}")->assertForbidden();
        $this->assertDatabaseHas('listings', ['id' => $listing->id]);
    }

    public function test_owner_can_update_and_delete(): void
    {
        $owner = User::factory()->create();
        $listing = $this->makeListing($owner);

        $this->actingAs($owner)->put("/listings/{$listing->id}", [
            'type' => 'product',
            'name' => 'Updated Laptop',
            'description' => 'Updated description text.',
            'category_id' => $listing->category_id,
            'subcategory_id' => $listing->subcategory_id,
            'country' => 'India',
            'state' => 'Punjab',
            'city' => 'Ludhiana',
            'area' => 'Sarabha Nagar',
            'price' => 20000,
        ])->assertRedirect(route('listings.my'));

        $this->assertDatabaseHas('listings', ['name' => 'Updated Laptop', 'city_slug' => 'ludhiana']);

        $this->actingAs($owner)->delete("/listings/{$listing->id}")->assertRedirect(route('listings.my'));
        $this->assertDatabaseMissing('listings', ['id' => $listing->id]);
    }

    public function test_category_city_and_city_category_filters(): void
    {
        $user = User::factory()->create();
        $this->makeListing($user, ['name' => 'Jalandhar Laptop']);
        $this->makeListing($user, ['name' => 'Ludhiana Laptop', 'city' => 'Ludhiana']);
        $vehicles = Category::where('slug', 'vehicles')->first();
        $bikes = Category::where('slug', 'bikes')->first();
        $this->makeListing($user, [
            'name' => 'Jalandhar Bike',
            'category_id' => $vehicles->id,
            'subcategory_id' => $bikes->id,
        ]);

        $this->get('/category/electronics')->assertOk()
            ->assertSee('Jalandhar Laptop')->assertSee('Ludhiana Laptop')->assertDontSee('Jalandhar Bike');

        $this->get('/category/electronics?sub=laptops')->assertOk()->assertSee('Jalandhar Laptop');
        $this->get('/category/electronics?sub=mobiles')->assertOk()->assertDontSee('Jalandhar Laptop');

        $this->get('/city/jalandhar')->assertOk()
            ->assertSee('Jalandhar Laptop')->assertSee('Jalandhar Bike')->assertDontSee('Ludhiana Laptop');

        $this->get('/city/jalandhar/category/electronics')->assertOk()
            ->assertSee('Jalandhar Laptop')->assertDontSee('Jalandhar Bike')->assertDontSee('Ludhiana Laptop');

        $this->get('/city/delhi')->assertOk()->assertSee('No listings in Delhi yet');
    }

    public function test_subcategory_url_redirects_to_parent_with_filter(): void
    {
        $this->get('/category/mobiles')->assertRedirect('/category/electronics?sub=mobiles');
    }

    public function test_details_page_shows_all_fields_and_404_for_missing(): void
    {
        $user = User::factory()->create(['name' => 'Seller Sam']);
        $listing = $this->makeListing($user);

        $this->get("/listings/{$listing->id}")->assertOk()
            ->assertSee('Test Laptop')->assertSee('Seller Sam')
            ->assertSee('Electronics')->assertSee('Laptops')
            ->assertSee('Punjab')->assertSee('Model Town');

        $this->get('/listings/99999')->assertNotFound();
    }

    public function test_search_filters_homepage(): void
    {
        $user = User::factory()->create();
        $this->makeListing($user, ['name' => 'Gaming Laptop']);

        $this->get('/?q=gaming')->assertOk()->assertSee('Gaming Laptop');
        $this->get('/?q=zzzz')->assertOk()->assertSee('No listings found');
    }

    private function pngFile(string $name = 'photo.png'): UploadedFile
    {
        // Real 1x1 PNG (no GD extension needed)
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    private function listingPayload(Listing $listing, array $extra = []): array
    {
        return array_merge([
            'type' => 'product',
            'name' => $listing->name,
            'description' => 'Updated description text.',
            'category_id' => $listing->category_id,
            'subcategory_id' => $listing->subcategory_id,
            'country' => 'India',
            'state' => 'Punjab',
            'city' => 'Jalandhar',
            'area' => 'Model Town',
            'price' => 1000,
        ], $extra);
    }

    public function test_user_can_upload_photo_replace_and_remove_it(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $cat = Category::where('slug', 'electronics')->first();
        $sub = Category::where('slug', 'mobiles')->first();

        $this->actingAs($owner)->post('/listings', [
            'type' => 'product', 'name' => 'Phone with photo',
            'description' => 'Very good condition phone.',
            'category_id' => $cat->id, 'subcategory_id' => $sub->id,
            'country' => 'India', 'state' => 'Punjab', 'city' => 'Jalandhar',
            'area' => 'Model Town', 'price' => 9000,
            'image' => $this->pngFile(),
        ])->assertRedirect(route('listings.my'));

        $listing = Listing::where('name', 'Phone with photo')->first();
        $this->assertNotNull($listing->image_path);
        Storage::disk('public')->assertExists($listing->image_path);
        $this->assertStringContainsString('/storage/listings/', $listing->image_src);
        $this->get("/listings/{$listing->id}")->assertOk()->assertSee($listing->image_path);

        // Replace: old file removed, new file stored
        $old = $listing->image_path;
        $this->actingAs($owner)->put("/listings/{$listing->id}", $this->listingPayload($listing, [
            'image' => $this->pngFile('new.png'),
        ]))->assertRedirect(route('listings.my'));
        $listing->refresh();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($listing->image_path);

        // Remove photo with the checkbox
        $current = $listing->image_path;
        $this->actingAs($owner)->put("/listings/{$listing->id}", $this->listingPayload($listing, [
            'remove_image' => '1',
        ]))->assertRedirect(route('listings.my'));
        $listing->refresh();
        $this->assertNull($listing->image_path);
        Storage::disk('public')->assertMissing($current);
    }

    public function test_deleting_listing_deletes_its_photo_and_bad_files_are_rejected(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $cat = Category::where('slug', 'electronics')->first();
        $sub = Category::where('slug', 'mobiles')->first();
        $base = [
            'type' => 'product', 'name' => 'Phone', 'description' => 'Very good condition phone.',
            'category_id' => $cat->id, 'subcategory_id' => $sub->id,
            'country' => 'India', 'state' => 'Punjab', 'city' => 'Jalandhar',
            'area' => 'Model Town', 'price' => 9000,
        ];

        // A non-image file is rejected
        $this->actingAs($owner)->post('/listings', $base + [
            'image' => UploadedFile::fake()->createWithContent('evil.php', '<?php echo 1;'),
        ])->assertSessionHasErrors('image');
        $this->assertDatabaseCount('listings', 0);

        // Valid upload, then delete the listing
        $this->actingAs($owner)->post('/listings', $base + ['image' => $this->pngFile()]);
        $listing = Listing::first();
        $path = $listing->image_path;
        Storage::disk('public')->assertExists($path);

        $this->actingAs($owner)->delete("/listings/{$listing->id}");
        Storage::disk('public')->assertMissing($path);
    }

    public function test_create_and_edit_forms_render_for_owner(): void
    {
        $owner = User::factory()->create();
        $listing = $this->makeListing($owner, ['image_url' => 'https://example.com/a.jpg']);

        $this->actingAs($owner)->get('/listings/create')->assertOk()
            ->assertSee('multipart/form-data', false)->assertSee('name="image"', false);
        $this->actingAs($owner)->get("/listings/{$listing->id}/edit")->assertOk()
            ->assertSee('https://example.com/a.jpg', false);
    }
}
