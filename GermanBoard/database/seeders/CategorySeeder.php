<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name_en' => 'coding',
                'name_ar' => 'كود',
                'created_at' => now(),
                'updated_at' => now(),
            ],
//            [
//                'name' => 'AI',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'name' => 'SE',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'name' => 'Science',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'name' => 'Technology',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
        ]);
    }
}
