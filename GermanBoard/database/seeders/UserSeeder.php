<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function create_data(): array
    {
        return [
//            [
//                'email' => 'trainee@test.com',
//                'email_verified_at' => now(),
//                'password' => bcrypt('12345678'),
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'email' => 'provider@test.com',
//                'email_verified_at' => now(),
//                'password' => bcrypt('12345678'),
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
            [
                'email' => 'admin@germanboard.app',
                'email_verified_at' => now(),
                'password' => bcrypt('2klb8559o04n'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            //            [
            //                'email' => 'test4@test.com',
            //                'email_verified_at' => now(),
            //                'password' => bcrypt('12345678'),
            //                'created_at' => now(),
            //                'updated_at' => now(),
            //            ],
            //            [
            //                'email' => 'test5@test.com',
            //                'email_verified_at' => now(),
            //                'password' => bcrypt('12345678'),
            //                'created_at' => now(),
            //                'updated_at' => now(),
            //            ],
            //            [
            //                'email' => 'test6@test.com',
            //                'email_verified_at' => now(),
            //                'password' => bcrypt('12345678'),
            //                'created_at' => now(),
            //                'updated_at' => now(),
            //            ],
            //            [
            //                'email' => 'test7@test.com',
            //                'email_verified_at' => now(),
            //                'password' => bcrypt('12345678'),
            //                'created_at' => now(),
            //                'updated_at' => now(),
            //            ],
            //            [
            //                'email' => 'test8@test.com',
            //                'email_verified_at' => now(),
            //                'password' => bcrypt('12345678'),
            //                'created_at' => now(),
            //                'updated_at' => now(),
            //            ],
            //            [
            //                'email' => 'test9@test.com',
            //                'email_verified_at' => now(),
            //                'password' => bcrypt('12345678'),
            //                'created_at' => now(),
            //                'updated_at' => now(),
            //            ],
            //            [
            //                'email' => 'test10@test.com',
            //                'email_verified_at' => now(),
            //                'password' => bcrypt('12345678'),
            //                'created_at' => now(),
            //                'updated_at' => now(),
            //            ]
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = $this->create_data();

//        $trainee = $this->userRepository->createUser($data[0]);
//        $provider = $this->userRepository->createUser($data[1]);
        $admin = $this->userRepository->createUser($data[0]);
        Provider::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'user_id' => $admin->id,
            'phone_number' => '0123456789',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'brief' => 'test brief',
            'specialized_at' => 'test specialized_at',
            'balance' => 0 ,
            'created_at' => now(),
            'updated_at' => now(),
        ],);
//        $trainee->assignRole(['trainee']);
//        $provider->assignRole(['trainer', 'provider']);
        $admin->assignRole(['admin']);
    }
}
