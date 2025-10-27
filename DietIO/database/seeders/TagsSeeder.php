<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Tags;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'overweight',
            'Maintaining weight',
            'Weight loss',
            'Special for diabetics',
            'Special for heart patients',
            'Special for patients with high blood pressure'
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'tag' => $tag
            ]);
        }
    }
}
