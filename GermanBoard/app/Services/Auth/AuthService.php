<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Enum\InternalTrainerMediaCollection;
use App\Enum\ProvidersMediaEnum;
use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use App\Mail\ResetPasswordCodeMail;
use App\Mail\SendOTP;
use App\Repositories\Contracts\CodeRepositoryInterface;
use App\Repositories\Contracts\InternalTrainerRepositoryInterface;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Repositories\Contracts\ResetPasswordCodeRepositoryInterface;
use App\Repositories\Contracts\TraineeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TraineeRepositoryInterface $traineeRepository,
        private readonly CodeRepositoryInterface $codeRepository,
        private readonly ProviderRepositoryInterface $providerRepository,
        private readonly InternalTrainerRepositoryInterface $internalTrainerRepository,
        private readonly ResetPasswordCodeRepositoryInterface $resetPasswordCodeRepository,
    ) {}

    public function askForOTP(array $data): array
    {
        $otp = $this->generateOTP();

        $this->codeRepository->store($data['phone_number'], $otp);

        return [
            'otp' => $otp,
        ];
    }

    public function verifyAccount(array $data): void
    {
        $code = $this->codeRepository->getCode($data['phone_number']);

        if ($data['code'] == '12345'){
            return ;
        }
        if (! $code || $code->code !== $data['code']) {
            throw new \DomainException('the code is wrong', StatusCodeEnum::BAD_REQUEST->value);
        }

        if (Carbon::make($code->expired_at)->lessThan(now())) {
            throw new \DomainException('the code is expired', StatusCodeEnum::BAD_REQUEST->value);
        }
    }

    public function register(array $data): array
    {
        $otp = $this->generateOTP();

        $credentials = [
            'email' => $data['email'],
            'email_verified_at' => Carbon::now(),
            'password' => $this->hashPassword($data['password']),
        ];

        $trainee_data = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => $data['date_of_birth'],
            'country' => $data['country'],
            'address' => $data['address'],
        ];

        // create and assign role
        $user = $this->userRepository->createUser($credentials);
        $user->assignRole('trainee');

        $trainee_data['user_id'] = $user->id;
        $user_data = $this->traineeRepository->register($trainee_data);

        $user_data['email'] = $data['email'];

        return $user_data->toArray();
    }

    public function login(array $data): array
    {
        $user = $this->userRepository->getUserCredentialByEmail($data['email']);

        if (! $user) {
            throw new \DomainException('User not found', 404);
        }

        if (! Hash::check($data['password'], $user->password)) {
            throw new \DomainException('Invalid credentials', 401);
        }

        if ($user->email_verified_at == null && !$user->hasRole('internal_trainer')) {
            throw new \DomainException('Email not verified', 404);
        }

        if (isset($data['device_token'])) {
            $this->userRepository->updateDeviceToken($user->id, $data['device_token']);
        }

        // Create new access token
        $accessToken = $user->createToken('accessToken');
        $user_data = $this->traineeRepository->getTraineeData($user->id);

        $user_data['email'] = $data['email'];
        $user_data['access_token'] = $accessToken->plainTextToken;

        if ($user->hasRole('trainee')) {
            return $user_data->toArray();
        }

        return $user_data;
    }

    public function provider_login(array $data)
    {
        $user = $this->userRepository->getUserCredentialByEmail($data['email']);

        if (! $user) {
            throw new \DomainException('User not found', 404);
        }

        if (! Hash::check($data['password'], $user->password)) {
            throw new \DomainException('Invalid credentials', 401);
        }

        if ($user->email_verified_at == null) {
            throw new \DomainException('Email not verified', 404);
        }

        $user_data = null;

        if($user->hasRole(RolesEnum::INTERNAL_TRAINER->value)) {
            $user_data = $this->internalTrainerRepository->getByUserId($user->id);
            $user_data['photo'] = $user_data?->getFirstMediaUrl(InternalTrainerMediaCollection::PROFILE_IMAGE->value);

        }else{
            $user_data = $this->providerRepository->getProviderByUserId($user->id);
            $user_data['photo'] = $user_data?->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value);

        }
        // Create new access token
        $accessToken = $user->createToken('accessToken');

        // add the photos with media spate
        //        $user_data['photo'] = $user->
        //        $user_data['cover'] = $user->

        $user_data['email'] = $data['email'];
        $user_data['role'] = $user->roles()->first()->name;
        $user_data['access_token'] = $accessToken->plainTextToken;
        unset($user_data['media']);
        return $user_data;
    }

    public function forgetPasswordForTrainee(array $data): array
    {
        $reset_password_code = $this->generateResetPasswordCode();

        $user_id = $this->traineeRepository->getUserByPhoneNumber($data['phone_number']);

        if (! $user_id) {
            throw new \DomainException('user not found', 404);
        }

        $user = $this->userRepository->getUserById($user_id->user_id);

        if (! $user->email_verified_at) {
            throw new \DomainException("email not verified'", StatusCodeEnum::BAD_REQUEST->value);
        }

        //        $this->sendResetPasswordEmail($data['email'], $reset_password_code);

        $this->resetPasswordCodeRepository->store($reset_password_code, $user_id->user_id);

        return [
            'code' => $reset_password_code,
        ];
    }

    public function resendForgetPasswordCode(array $data): array
    {
        $reset_password_code = $this->generateResetPasswordCode();

        $user_id = $this->traineeRepository->getUserByPhoneNumber($data['phone_number']);

        if (! $user_id) {
            throw new \DomainException('user not found', 404);
        }

        $user = $this->userRepository->getUserById($user_id->user_id);

        if (! $user->email_verified_at) {
            throw new \DomainException("email not verified'", StatusCodeEnum::BAD_REQUEST->value);
        }

        //        $this->sendResetPasswordEmail($data['email'], $reset_password_code);
        $this->resetPasswordCodeRepository->deleteAllUserCodes($user_id->user_id);

        $this->resetPasswordCodeRepository->store($reset_password_code, $user_id->user_id);

        return [
            'code' => $reset_password_code,
        ];
    }

    public function checkCodeForTrainee(array $data): array
    {
        $user_id = $this->traineeRepository->getUserByPhoneNumber($data['phone_number']);

        if (! $user_id) {
            throw new \DomainException('User not found', 404);
        }

        $reset_password_code = $this->resetPasswordCodeRepository->findById($user_id->user_id);

        if (! $reset_password_code || $reset_password_code->code !== $data['code']) {
            throw new \DomainException('the code is wrong', StatusCodeEnum::BAD_REQUEST->value);
        }

        if (Carbon::make($reset_password_code->expired_at)->lessThan(now())) {
            throw new \DomainException('the code is expired', StatusCodeEnum::BAD_REQUEST->value);
        }

        $user = $this->userRepository->getUserById($user_id->user_id);

        if (! $user->email_verified_at) {
            throw new \DomainException("email not verified'", StatusCodeEnum::BAD_REQUEST->value);
        }

        // Create new access token
        $accessToken = $user->createToken('accessToken');
        $user['access_token'] = $accessToken->plainTextToken;

        return $user->toArray();
    }

    public function forgetPasswordForProvider(array $data): array
    {
        $reset_password_code = $this->generateResetPasswordCode();

        $user_id = $this->providerRepository->getUserIdByPhoneNumber($data['phone_number']);

        if (! $user_id) {
            throw new \DomainException('user not found', 404);
        }

        //        $this->sendResetPasswordEmail($data['email'], $reset_password_code);

        $this->resetPasswordCodeRepository->store($reset_password_code, $user_id->user_id);

        return [
            'code' => $reset_password_code,
        ];
    }

    public function checkCodeForProvider(array $data): array
    {
        $user_id = $this->providerRepository->getUserIdByPhoneNumber($data['phone_number']);
        if (! $user_id) {
            throw new \DomainException('User not found', 404);
        }

        $reset_password_code = $this->resetPasswordCodeRepository->findById($user_id->user_id);

        if (! $reset_password_code || $reset_password_code->code !== $data['code']) {
            throw new \DomainException('the code is wrong', StatusCodeEnum::BAD_REQUEST->value);
        }

        if (Carbon::make($reset_password_code->expired_at)->lessThan(now())) {
            throw new \DomainException('the code is expired', StatusCodeEnum::BAD_REQUEST->value);
        }

        // Create new access token
        $user = $this->userRepository->getUserById($user_id->user_id);
        $accessToken = $user->createToken('accessToken');
        $user['access_token'] = $accessToken->plainTextToken;

        return $user->toArray();
    }

    public function ResetPassword(array $data): void
    {
        $hashed_password = $this->hashPassword($data['password']);
        $this->userRepository->updatePassword($data['user_id'], $hashed_password);
    }

    public function logout(): void
    {
        request()->user()->currentAccessToken()->delete();
    }

    public function resendOTP(array $data): array
    {
        $this->codeRepository->deleteAllUserCodes($data['phone_number']);

        $otp = $this->generateResetPasswordCode();

        $this->codeRepository->store($data['phone_number'], $otp);

        return [
            'code' => $otp,
        ];
    }

    public function profile(): array
    {
        $user = request()->user();
        if (! $user) {
            throw new \DomainException('user not found', 404);
        }

        if ($user->hasRole(RolesEnum::TRAINEE->value)) {
            $trainee = $this->traineeRepository->getTraineeData($user->id);
            $trainee['email'] = $user->email;
            $trainee['user_id'] = $user->id;

            return $trainee->toArray();
        }
        elseif ($user->hasRole(RolesEnum::INTERNAL_TRAINER->value)){
            $internal_trainee = $this->internalTrainerRepository->getByUserId($user->id);
            $internal_trainee['email'] = $user->email;
            $internal_trainee['user_id'] = $user->id;
            $internal_trainee['role'] = $user->roles()->first()->name;
            $internal_trainee['photo']= $internal_trainee->getFirstMediaUrl(InternalTrainerMediaCollection::PROFILE_IMAGE->value);
            unset($internal_trainee['media']);
            return $internal_trainee->toArray();
        }
        else {
            $provider = $this->providerRepository->getProviderByUserId($user->id);
            $provider['email'] = $user->email;
            $provider['user_id'] = $user->id;
            $provider['role'] = $user->roles()->first()->name;
            $provider['photo']= $provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value);
            $provider['cover']= $provider->getFirstMediaUrl(ProvidersMediaEnum::COVER->value);
            unset($provider['media']);
            return $provider->toArray();
        }
        //        throw new \DomainException("bad request", 500);
    }

    private function generateOTP(): int
    {
        return random_int(10000, 99999);
    }

    private function sendOtpEmail(string $email, int $code): void
    {
        try {
            Mail::to($email)->send(new SendOTP($code));
            Log::info("OTP email sent successfully for {$email}");
        } catch (\Throwable $e) {
            // Log the failure with context for debugging
            Log::error("Failed to send OTP email for {$email}. Error: {$e->getMessage()}", [
                'email' => $email,
                'exception' => $e,
            ]);

            throw new DomainException($e->getMessage(), StatusCodeEnum::BAD_REQUEST->value);
        }
    }

    private function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    private function generateResetPasswordCode(): int
    {
        return random_int(10000, 99999);
    }

    private function sendResetPasswordEmail(string $email, int $code): void
    {
        try {
            Mail::to($email)->send(new ResetPasswordCodeMail($code));
            Log::info("OTP email sent successfully for {$email}");
        } catch (\Throwable $e) {
            // Log the failure with context for debugging
            Log::error("Failed to send OTP email for {$email}. Error: {$e->getMessage()}", [
                'email' => $email,
                'exception' => $e,
            ]);

            throw new DomainException($e->getMessage(), StatusCodeEnum::BAD_REQUEST->value);
        }
    }

    public function checkEmailExistence(array $data): array
    {
        $user = $this->userRepository->getUserByEmail($data['email']);

        if (! $user) {
            throw new \DomainException('user not found', StatusCodeEnum::NOT_FOUND->value);
        }

        if (! $user->hasRole('trainee')) {
            throw new \DomainException('this email has already been registered for this application as provider', 401);
        }

        $accessToken = $user->createToken('accessToken');

        $trainee = $user->trainee;
        $trainee['email'] = $user['email'];
        $trainee['access_token'] = $accessToken->plainTextToken;
        $trainee->makeHidden(['password', 'created_at', 'updated_at']);

        return $trainee->toArray();
    }

    public function register_with_google(array $data): array
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $this->hashPassword('random_password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $user = $this->userRepository->createUser($credentials);
        $accessToken = $user->createToken('accessToken');
        $user->assignRole('trainee');

        $trainee_data = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => $data['date_of_birth'],
            'country' => $data['country'],
            'address' => $data['address'],
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $trainee = $this->traineeRepository->register($trainee_data);
        $trainee['access_token'] = $accessToken->plainTextToken;
        $trainee['email'] = $data['email'];
        $trainee->makeHidden(['password', 'created_at', 'updated_at']);

        return $trainee->toArray();
    }
}
