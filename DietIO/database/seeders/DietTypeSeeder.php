<?php

namespace Database\Seeders;

use App\Models\TypeOfDiet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DietTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'type' => 'Vegetarian Diet',
                'description' => 'A vegetarian diet excludes meat, poultry, and seafood but may include dairy products and eggs. It focuses on plant-based foods like fruits, vegetables, grains, nuts, and seeds. Vegetarians often choose this diet for health, ethical, or environmental reasons.'
            ],
            [
                'type' => 'Vegan Diet',
                'description' => 'A vegan diet excludes all animal products, including meat, dairy, eggs, and honey. It consists of plant-based foods like fruits, vegetables, grains, legumes, nuts, and seeds. Vegans follow this diet for ethical, environmental, and health reasons.'
            ],
            [
                'type' => 'Keto Diet',
                'description' => 'The ketogenic diet is a high-fat, low-carb, moderate-protein diet that aims to induce a state of ketosis, where the body burns fat for fuel instead of carbohydrates. It typically involves consuming around 70-80% of calories from fat, 15-30% from protein, and 5% from carbohydrates. The diet is known for its potential benefits in weight loss, blood sugar control, and reducing heart disease risk factors.'
            ],
            [
                'type' => 'Moderate Diet',
                'description' => 'A moderate diet involves consuming a balanced amount of macronutrients, including carbohydrates, proteins, and fats. It focuses on portion control, variety, and moderation in food choices. This diet promotes overall health and well-being by emphasizing a balanced intake of nutrients without extreme restrictions.'
            ]
        ];
        foreach ($tags as $tag) {
            TypeOfDiet::create([
                'type' => $tag['type'],
                'description' => $tag['description']
            ]);
        }
    }
}
