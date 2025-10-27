<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function create_data(): array
    {
        return [
            [
                'first_name' => 'test1',
                'last_name' => 'test1',
                'user_id' => 2,
                'phone_number' => '0123456789',
                'date_of_birth' => '2000-01-01',
                'gender' => 'male',
                'brief' => 'test brief',
                'specialized_at' => 'test specialized_at',
                'balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            //            [
            //                'first_name' => 'test2',
            //                'last_name' => 'test2',
            //                'user_id' => 7,
            //                'phone_number' => '0123456789',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'brief' => 'test brief',
            //                'specialized_at' => 'test specialized_at',
            //                'photo' => 'test photo',
            //                'cover' => 'test cover',
            //                'created_at' => now(),
            //                'updated_at' => now()
            //            ],
            //            [
            //                'first_name' => 'test3',
            //                'last_name' => 'test3',
            //                'user_id' => 8,
            //                'phone_number' => '0123456789',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'brief' => 'test brief',
            //                'specialized_at' => 'test specialized_at',
            //                'photo' => 'test photo',
            //                'cover' => 'test cover',
            //                'created_at' => now(),
            //                'updated_at' => now()
            //            ],
            //            [
            //                'first_name' => 'test4',
            //                'last_name' => 'test4',
            //                'user_id' => 9,
            //                'phone_number' => '0123456789',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'brief' => 'test brief',
            //                'specialized_at' => 'test specialized_at',
            //                'photo' => 'test photo',
            //                'cover' => 'test cover',
            //                'created_at' => now(),
            //                'updated_at' => now()
            //            ],
            //            [
            //                'first_name' => 'test5',
            //                'last_name' => 'test5',
            //                'user_id' => 10,
            //                'phone_number' => '0123456789',
            //                'date_of_birth' => '2000-01-01',
            //                'gender' => 'male',
            //                'brief' => 'test brief',
            //                'specialized_at' => 'test specialized_at',
            //                'photo' => 'test photo',
            //                'cover' => 'test cover',
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
        Provider::insert($data);
    }
}
