<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            TagsSeeder::class,
            Type_of_mealsSeeder::class,
            ActivitiesSeeder::class,
            DietTypeSeeder::class,
            DoctorSeeder::class,
            UserSeeder::class,
            // FilesSeeder::class,
            PaymentRequestSeeder::class,
            SubscribingRequestSeeder::class,

        ]);
    }
}
