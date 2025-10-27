<?php

namespace Database\Seeders;

use App\Models\Trainee;
use App\Models\User;
use Illuminate\Database\Seeder;

class TraineeSeeder extends Seeder
{
    public function create_data(): array
    {
        return [
            [
                'first_name' => 'test1',
                'last_name' => 'test1',
                'user_id' => 1,
                'phone_number' => '1111111111',
                'date_of_birth' => '2000-01-01',
                'gender' => 'male',
                'country' => 'test country',
                'address' => 'test address',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            //            [
            //                'first_name' => 'test2',
            //                'last_name' => 'test2',
            //                'user_id' => 2,
            //                'phone_number' => '2222222222',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'country' => 'test country',
            //                'address' => 'test address',
            //                'created_at' => now(),
            //                'updated_at' => now()
            //            ],
            //            [
            //                'first_name' => 'test3',
            //                'last_name' => 'test3',
            //                'user_id' => 3,
            //                'phone_number' => '3333333333',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'country' => 'test country',
            //                'address' => 'test address',
            //                'created_at' => now(),
            //                'updated_at' => now()
            //            ],
            //            [
            //                'first_name' => 'test4',
            //                'last_name' => 'test4',
            //                'user_id' => 4,
            //                'phone_number' => '4444444444',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'country' => 'test country',
            //                'address' => 'test address',
            //                'created_at' => now(),
            //                'updated_at' => now()
            //            ],
            //            [
            //                'first_name' => 'test5',
            //                'last_name' => 'test5',
            //                'user_id' => 5,
            //                'phone_number' => '5555555555',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'country' => 'test country',
            //                'address' => 'test address',
            //                'created_at' => now(),
            //                'updated_at' => now()
            //            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = $this->create_data();

        Trainee::insert($data);
    }
}
