<?php

namespace Database\Seeders;

use App\Models\SubscribingRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscribingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointments = [
            [
                'user_id' => 1,
                'doctor_id' => 1,
                'message' => 'Appointment for consultation',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'doctor_id' => 2,
                'message' => 'Follow-up appointment',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'doctor_id' => 1,
                'message' => 'Routine check-up',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'doctor_id' => 3,
                'message' => 'Specialist consultation',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'doctor_id' => 2,
                'message' => 'Second opinion',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'doctor_id' => 1,
                'message' => 'Prescription refill',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'doctor_id' => 2,
                'message' => 'Surgery consultation',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'doctor_id' => 3,
                'message' => 'Physical therapy session',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'doctor_id' => 1,
                'message' => 'Dental check-up',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'doctor_id' => 5,
                'message' => 'MRI appointment',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'doctor_id' => 5,
                'message' => 'Eye exam',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($appointments as $appointmentData) {
            SubscribingRequest::create($appointmentData);
        }

    }
}
