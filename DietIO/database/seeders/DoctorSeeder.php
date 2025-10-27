<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'firstName' => 'Alice',
                'lastName' => 'Brown',
                'email' => 'alice.brown@example.com',
                'password' => bcrypt('password123'),
                'phoneNumber' => '123-456-7890',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firstName' => 'Michael',
                'lastName' => 'Johnson',
                'email' => 'Michael.Johnson@example.com',
                'password' => bcrypt('secret123'),
                'phoneNumber' => '987-654-3210',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firstName' => 'Sarah',
                'lastName' => 'Lee',
                'email' => 'Sarah.Lee@example.com',
                'password' => bcrypt('secret123'),
                'phoneNumber' => '654-987-3210',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firstName' => 'Mark',
                'lastName' => 'Alison',
                'email' => 'Mark.Alison@example.com',
                'password' => bcrypt('secret123'),
                'phoneNumber' => '123-789-4560',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'firstName' => 'Rose',
                'lastName' => 'Mary',
                'email' => 'Rose.Mary@example.com',
                'password' => bcrypt('secret123'),
                'phoneNumber' => '123-789-4560',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($doctors as $doctor)
        {
            Doctor::create($doctor);
        }
    }
}
