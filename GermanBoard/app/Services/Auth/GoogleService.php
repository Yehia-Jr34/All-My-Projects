<?php

namespace App\Services\Auth;

use App\Repositories\Contracts\TraineeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TraineeRepositoryInterface $traineeRepository,
    ) {}

    public function redirectToGoogle(): RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): array
    {
        $googleUser = Socialite::driver('google')->user();

        $user_data = [
            'email' => $googleUser->getEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('random_password'), // Generate a random password since Google Login is used
        ];

        $trainee_data = [
            'first_name' => $googleUser->user['given_name'] ?? null,
            'last_name' => $googleUser->user['family_name'] ?? null,
            'phone_number' => '',       // Google doesn't provide this by default
            'date_of_birth' => '',       // Google doesn't provide this by default
            'country' => '',             // Google doesn't provide this by default
            'address' => '',             // Google doesn't provide this by default
            'gender' => '',               // Google doesn't provide this by default
        ];

        Role::findOrCreate('trainee', 'web');

        $user = $this->userRepository->createUser($user_data);
        $user->assignRole('trainee');

        $trainee_data['user_id'] = $user->id;

        $this->traineeRepository->register($trainee_data);

        $accessToken = $user->createToken('accessToken')->plainTextToken;

        $merged_data = array_merge($user_data, $trainee_data);

        $merged_data['access_token'] = $accessToken;

        return $merged_data;

    }
}
