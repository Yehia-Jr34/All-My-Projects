<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'activity' => 'never',
            ],
            [
                'activity' => 'low',
            ],
            [
                'activity' => 'medium',
            ],
            [
                'activity' => 'high',
            ],
            [
                'activity' => 'extreme',
            ],
        ];
        foreach ($tags as $tag) {
            Activity::create([
                'activity' => $tag['activity'],
            ]);
        }
    }
}
