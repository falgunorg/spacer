<?php

namespace Database\Seeders; // 1. Added namespace

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // 2. Added DB facade import
use App\Models\User; // 3. Updated User model path (Laravel 10 standard)

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin1234'),
                'created_at' => now(), // Using now() helper is cleaner
                'role' => 'admin'
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@mail.com',
                'password' => bcrypt('staff1234'),
                'created_at' => now(),
                'role' => 'staff'
            ],
        ]);

        // Category Seeding (IDs will be auto-generated)
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Household & Cleanings'],
            ['name' => 'Clothings'],
            ['name' => 'Sports & Outdoors'],
            ['name' => 'Animals & Pet Supplies'],
            ['name' => 'Arts & Entertainment'],
            ['name' => 'Luggage & Bags'],
            ['name' => 'Hardware'],
            ['name' => 'Cameras & Optics'],
            ['name' => 'Home & Garden'],
            ['name' => 'Apparel & Accessories'],
            ['name' => 'Vehicles & Parts'],
            ['name' => 'Business & Industrial'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
