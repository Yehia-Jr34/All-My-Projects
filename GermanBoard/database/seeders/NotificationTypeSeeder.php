<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NotificationType::insert(
            [
                [
                    'type' => 'new_training_published',
                    'icon' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'session_delayed',
                    'icon' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'session_deleted',
                    'icon' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'session_well_start_in_30_minutes',
                    'icon' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                [
                    'type' => 'payment_failed',
                    'icon' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                [
                    'type' => 'payment_succeeded',
                    'icon' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                [
                    'type' => 'enrolled_in_training',
                    'icon' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],


            ]
        );
    }
}
