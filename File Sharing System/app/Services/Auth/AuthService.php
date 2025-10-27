<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTOs\Auth\UserLoginDTO;
use App\DTOs\Auth\UserRegistrationDTO;
use App\DTOs\Auth\CheckCodeDTO;
use App\DTOs\User\FilteredUserDTO;
use App\Mail\SendOTP;
use App\Repositories\Contracts\CodeRepositoryInterface;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;
use App\Repositories\Contracts\UserImageRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    /**
     * Access token duration in minutes
     */
    private const ACCESS_TOKEN_DURATION = 100;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected CodeRepositoryInterface $codeRepository,
        protected UserImageRepositoryInterface $userImageRepository,
        protected RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {}

    public function register(UserRegistrationDTO $userInformation): array
    {
        $code = $this->generateOTP();

        $this->sendOtpEmail($userInformation->email, $code);

        $image_name = $userInformation->username . "." . $userInformation->image->extension();
        $userInformation->image->move(public_path('images/profile_images'), $image_name);
        $image_path = 'images/profile_images/' . $image_name;

        $userInformation =  $this->userRepository->createUser($userInformation);

        $this->userImageRepository->store($userInformation->id, $image_path);

        $this->codeRepository->storeCode($code, $userInformation->id);

        return $userInformation->toArrayWithImage(url($image_path), $code);
    }

    public function login(UserLoginDTO $userLoginDTO)
    {
        $userTmp = $this->userRepository->getUserByEmail($userLoginDTO->email);

        if ($userTmp) {
            if (!$userTmp->email_verified_at) {
                return [
                    'message' => 'Email not verified',
                    'data' => ''
                ];
            }

            if (Auth::attempt(['email' => $userLoginDTO->email, 'password' => $userLoginDTO->password])) {
                // Delete all existing refresh tokens
                $this->refreshTokenRepository->deleteAllUserTokens($userTmp->id);

                // Create new access token that expires in 5 minutes
                $accessToken = $userTmp->createToken(
                    'accessToken',
                    ['*'],
                    now()->addMinutes(self::ACCESS_TOKEN_DURATION)
                );

                // Create new refresh token
                $refreshToken = $this->refreshTokenRepository->createToken($userTmp);

                $message = 'logged in successfully';

                $data['user'] = FilteredUserDTO::fromUser($userTmp);
                if ($this->userImageRepository->getUserImage($userTmp->id)) {
                    $image_path = $this->userImageRepository->getUserImage($userTmp->id)?->path;
                    $data['image_path'] = url($image_path);
                }
                $data['image_path'] = null;
                $data['access_token'] = $accessToken->plainTextToken;
                $data['refresh_token'] = $refreshToken->token;


                return [
                    'message' => $message,
                    'data' => $data
                ];
            } else {
                return [
                    'message' => 'Wrong credential',
                    'data' => ''
                ];
            }
        } else {
            return [
                'message' => 'User not found',
                'data' => ''
            ];
        }
    }

    public function sendCode(string $email)
    {
        $code = $this->generateOTP();

        $user = $this->userRepository->getUserDTOByEmail($email);

        if ($user) {
            $this->codeRepository->storeCode($code, $user->id);
            try {
                Mail::to($email)->send(new SendOTP($code));

                return true;
            } catch (\Exception $e) {
                \Log::error("Failed to send email to {$email}: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    public function checkCode(CheckCodeDTO $checkCodeDTO): array
    {
        $user = $this->userRepository->getUserByEmail($checkCodeDTO->email);

        if (!$user) {
            throw new \DomainException("User not found");
        }

        $code = $this->codeRepository->getCodeByUserId($user->id);

        if (!$code || $code->code !== $checkCodeDTO->code) {
            throw new \DomainException("the code is wrong");
        }

        $this->codeRepository->deleteAllUserCodes($user->id);

        return [
            'access_token' => $user->createToken(
                'accessToken',
                ['*'],
                now()->addMinutes(self::ACCESS_TOKEN_DURATION)
            )->plainTextToken,
            'refresh_token' => $this->refreshTokenRepository->createToken($user)->token,
        ];
    }

    public function verifyAccount(CheckCodeDTO $userVerifyDTO): array
    {
        $user = $this->userRepository->getUserByEmail($userVerifyDTO->email);

        if (!$user) {
            throw new \DomainException("User not found");
        }

        $code = $this->codeRepository->getCodeByUserId($user->id);

        if (!$code || $code->code !== $userVerifyDTO->code) {
            throw new \DomainException("the code is wrong");
        }

        if (Carbon::make($code->expired_at)->lessThan(now())) {
            throw new \DomainException("the code is expired");
        }

        $this->userRepository->setVerifyAt($user->id);

        $this->codeRepository->deleteAllUserCodes($user->id);

        return [
            'access_token' => $user->createToken(
                'accessToken',
                ['*'],
                now()->addMinutes(self::ACCESS_TOKEN_DURATION)
            )->plainTextToken,
            'refresh_token' => $this->refreshTokenRepository->createToken($user)->token,
        ];
    }

    public function resetPassword(string $password): void
    {
        $hashedPassword = $this->hashPassword($password);
        $this->userRepository->updatePassword($hashedPassword);
    }

    public function refresh(string $refreshToken)
    {
        $token = $this->refreshTokenRepository->findValidToken($refreshToken);

        if (!$token) {
            throw new \DomainException("Invalid or expired refresh token");
        }

        // Delete the used refresh token
        $this->refreshTokenRepository->deleteToken($refreshToken);

        $user = $token->user;

        // Create new access token
        $accessToken = $user->createToken(
            'accessToken',
            ['*'],
            now()->addMinutes(self::ACCESS_TOKEN_DURATION)
        );

        // Create new refresh token
        $newRefreshToken = $this->refreshTokenRepository->createToken($user);

        return [
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $newRefreshToken->token,
        ];
    }

    public function logout(string $refreshToken): bool
    {
        return $this->refreshTokenRepository->deleteToken($refreshToken);
    }

    // Helpers

    /**
     * Generate OTP
     */
    private function generateOTP(): int
    {
        return random_int(100000, 999999);
    }

    /**
     * Sends an OTP email asynchronously.
     */

    private function sendOtpEmail(string $email, int $code): void
    {
        try {
            Mail::to($email)->send(new SendOTP($code));
            \Log::info("OTP email sent successfully for {$email}");
        } catch (\Throwable $e) {
            // Log the failure with context for debugging
            \Log::error("Failed to send OTP email for {$email}. Error: {$e->getMessage()}", [
                'email' => $email,
                'exception' => $e
            ]);
        }
    }

    private function hashPassword(string $password): string
    {
        return Hash::make($password);
    }
}
