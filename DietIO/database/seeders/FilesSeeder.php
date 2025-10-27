<?php

namespace Database\Seeders;

use App\Models\Files;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = [
            [
                'user_id' => 1,
                'weight' => 70.5,
                'height' => 175.3,
                'age' => 30,
                'gender' => 'Male',
                'waistline' => 32.2,
                'buttocks_cir' => 40.1,
                'target' => 1,
                'number_of_meals' => 3,
                'activity_id' => 1,
                'type_of_diet_id' => 1,
                'diseases' => 'None',
                'surgery' => 'None',
                'wake_up' => '07:00:00',
                'sleep' => '23:00:00',
                'job' => 'Software Engineer',
                'study' => 'Computer Science',
                'daily_routine' => 'Wake up, exercise, work, eat, sleep',
                'notes' => 'This is a sample note',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'weight' => 65.8,
                'height' => 168.5,
                'age' => 28,
                'gender' => 'Female',
                'waistline' => 28.6,
                'buttocks_cir' => 36.7,
                'target' => 2,
                'number_of_meals' => 2,
                'activity_id' => 2,
                'type_of_diet_id' => 2,
                'diseases' => 'Hypertension',
                'surgery' => 'Appendectomy',
                'wake_up' => '06:30:00',
                'sleep' => '22:30:00',
                'job' => 'Teacher',
                'study' => 'Education',
                'daily_routine' => 'Morning walk, teach, grade papers, relax, sleep',
                'notes' => 'Regular exercise is recommended',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'weight' => 75.2,
                'height' => 180.1,
                'age' => 35,
                'gender' => 'Male',
                'waistline' => 34.5,
                'buttocks_cir' => 42.3,
                'target' => 3,
                'number_of_meals' => 4,
                'activity_id' => 3,
                'type_of_diet_id' => 3,
                'diseases' => 'Diabetes',
                'surgery' => 'None',
                'wake_up' => '05:30:00',
                'sleep' => '23:30:00',
                'job' => 'Accountant',
                'study' => 'Business',
                'daily_routine' => 'Morning jog, work, gym, dinner, read, sleep',
                'notes' => 'Monitor blood sugar levels',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'weight' => 62.9,
                'height' => 165.1,
                'age' => 24,
                'gender' => 'Male',
                'waistline' => 34.5,
                'buttocks_cir' => 42.3,
                'target' => 3,
                'number_of_meals' => 4,
                'activity_id' => 3,
                'type_of_diet_id' => 3,
                'diseases' => 'Diabetes',
                'surgery' => 'None',
                'wake_up' => '05:30:00',
                'sleep' => '23:30:00',
                'job' => 'Accountant',
                'study' => 'Business',
                'daily_routine' => 'Morning jog, work, gym, dinner, read, sleep',
                'notes' => 'Monitor blood sugar levels',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        foreach ($files as $file) {
            Files::create($file);
        }
    }
}
