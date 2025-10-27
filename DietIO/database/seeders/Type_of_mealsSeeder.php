<?php

namespace Database\Seeders;

use App\Models\TypeOfMeal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Type_of_mealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'breakfast',
            'lunch',
            'dinner',
            'snack',
        ];

        foreach ($tags as $tag) {
            TypeOfMeal::create([
                'type' => $tag
            ]);
        }
    }
}
