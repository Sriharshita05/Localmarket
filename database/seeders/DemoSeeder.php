<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Optional sample data for demos:  php artisan db:seed --class=DemoSeeder
 * Requires CategorySeeder to have run first.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::firstOrCreate(
            ['email' => 'seller@test.com'],
            ['name' => 'Test Seller', 'password' => 'password123']
        );

        // [category slug, subcategory slug, type, name, description, city, area, price]
        $rows = [
            ['electronics', 'mobiles', 'product', 'iPhone 13 128GB', 'Good condition, comes with box and charger. Battery health 89%.', 'Jalandhar', 'Model Town', 35000],
            ['electronics', 'laptops', 'product', 'Dell Inspiron 15 Laptop', 'i5 11th gen, 8GB RAM, 512GB SSD. Used for one year, no scratches.', 'Jalandhar', 'Urban Estate', 32000],
            ['electronics', 'mobiles', 'product', 'Samsung Galaxy S21', 'Excellent condition with original charger. Single owner.', 'Ludhiana', 'Sarabha Nagar', 28000],
            ['vehicles', 'bikes', 'product', 'Royal Enfield Classic 350', '2020 model, 18,000 km run, all papers clear.', 'Jalandhar', 'Basti Sheikh', 120000],
            ['home-services', 'plumbing', 'service', 'Expert Plumber - 24x7', 'Leakage repair, bathroom fitting and pipeline work. Same-day visit.', 'Jalandhar', 'Adarsh Nagar', 500],
            ['home-services', 'cleaning', 'service', 'Deep Home Cleaning', 'Full home deep cleaning with machines and eco-friendly products.', 'Ludhiana', 'Model Town', 1500],
            ['furniture', 'beds', 'product', 'Wooden King Size Bed', 'Solid sheesham wood, with storage. Mattress not included.', 'Amritsar', 'Ranjit Avenue', 18000],
            ['books-education', 'tuition', 'service', 'Maths Tuition for Class 9-12', 'Experienced teacher, home and online classes available.', 'Phagwara', 'GT Road', 800],
        ];

        foreach ($rows as [$cat, $sub, $type, $name, $desc, $city, $area, $price]) {
            $category = Category::where('slug', $cat)->first();
            $subcategory = Category::where('slug', $sub)->first();

            if (! $category || ! $subcategory) {
                continue;
            }

            Listing::firstOrCreate(
                ['user_id' => $seller->id, 'name' => $name],
                [
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                    'type' => $type,
                    'description' => $desc,
                    'country' => 'India',
                    'state' => 'Punjab',
                    'city' => $city,
                    'area' => $area,
                    'price' => $price,
                ]
            );
        }
    }
}
