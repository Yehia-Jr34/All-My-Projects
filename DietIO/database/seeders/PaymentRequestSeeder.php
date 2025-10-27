<?php

namespace Database\Seeders;

use App\Models\PaymentRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requests = [
            [
                'user_id' => 1,
                'doctor_id' => 1,
                'value' => 40000,
                'message' => 'message',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'doctor_id' => 1,
                'value' => 25000,
                'status' => 'approved',
                'message' => 'message',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'doctor_id' => 2,
                'value' => 100000,
                'message' => 'message',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'doctor_id' => 2,
                'value' => 225000,
                'message' => 'message',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'doctor_id' => 3,
                'message' => 'message',
                'value' => 275000,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'doctor_id' => 3,
                'message' => 'message',
                'value' => 500000,
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'doctor_id' => 2,
                'value' => 450000,
                'message' => 'message',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'doctor_id' => 3,
                'message' => 'message',
                'value' => 200000,
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'doctor_id' => 1,
                'value' => 250000,
                'message' => 'message',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'doctor_id' => 3,
                'value' => 400000,
                'status' => 'waiting',
                'created_at' => now(),
                'message' => 'message',
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'doctor_id' => 2,
                'value' => 350000,
                'message' => 'message',
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($requests as $request) {
            PaymentRequest::create($request);
        }
    }
}
