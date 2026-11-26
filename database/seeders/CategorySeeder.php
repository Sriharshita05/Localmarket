<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics' => ['icon' => 'fa-mobile-screen', 'subs' => ['Mobiles', 'Laptops', 'Televisions', 'Cameras', 'Accessories']],
            'Vehicles' => ['icon' => 'fa-car', 'subs' => ['Cars', 'Bikes', 'Scooters', 'Spare Parts']],
            'Property' => ['icon' => 'fa-house', 'subs' => ['For Sale', 'For Rent', 'Plots', 'PG & Hostels']],
            'Furniture' => ['icon' => 'fa-couch', 'subs' => ['Sofa & Chairs', 'Beds', 'Tables', 'Storage']],
            'Fashion' => ['icon' => 'fa-shirt', 'subs' => ['Men', 'Women', 'Kids', 'Footwear']],
            'Books & Education' => ['icon' => 'fa-book', 'subs' => ['Textbooks', 'Novels', 'Tuition', 'Courses']],
            'Home Services' => ['icon' => 'fa-screwdriver-wrench', 'subs' => ['Plumbing', 'Electrician', 'Cleaning', 'Repairs']],
            'Jobs' => ['icon' => 'fa-briefcase', 'subs' => ['Full Time', 'Part Time', 'Freelance']],
        ];

        foreach ($categories as $name => $data) {
            $parent = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'icon' => $data['icon'],
            ]);

            foreach ($data['subs'] as $sub) {
                Category::create([
                    'parent_id' => $parent->id,
                    'name' => $sub,
                    'slug' => Str::slug($sub),
                ]);
            }
        }
    }
}