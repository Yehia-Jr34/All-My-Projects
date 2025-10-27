<?php

namespace App\Http\Controllers\API;

use App\Events\UserRegistered;
use App\Events\UserVerifiedEmail;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetEmail;
use App\Mail\VerifyEmail;
use App\Models\Activity;
use App\Models\Files;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Laravel\Socialite\Facades\Socialite;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:60|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
//            'password' => $request->password,
        ]);
        if (!$user) {
            return response()->JSON([
                'success' => false
            ]);
        }

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('auth_token')->plainTextToken;

        $user_wallet = UserWallet::create([
            'user_id' => $user->id
        ]);

        event(new UserRegistered($user));

        return response()->json([
            'message' => 'Registration successful. Please check your email for verification.',
            'data' => [
                'user' => $user,
                'wallet' => $user_wallet,
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
            'success'=> true,
            'role' => 'USER',
        ], 201); // 201 Created status code
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('user')->attempt($credentials)) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'doctor' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => 'user',
        ], 200);
    }

    public function logout(Request $request)
    {
        if ($request->user() != null) {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
        return response()->json([
            'message' => 'Not authorized'
        ]);
    }

    // email verification
    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));

        if ($user && hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification()))) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }
        }

//        event(new UserVerifiedEmail($user));

        return response()->json([
            'message' => 'Verified'
        ]);
    }

    //reset Password
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            $user = User::where('email', $request->email)->first();
            Mail::to($user)->send(new PasswordResetEmail($user));
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
//                    'password' => $password
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withErrors(['email' => [__($status)]]);
    }

    //login with Google
    public function loginWithGoogle(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->getId()],
            [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Str::password(12),
            ]
        );

        if ($user->email_verified_at == null) {
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        $user_wallet = UserWallet::firstOrCreate([
            'user_id' => $user->id
        ]);

        event(new UserRegistered($user));

        return response()->json([
            'message' => 'LoggedIn',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user
        ], 200);
    }

    //update account
    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'password' => 'required|confirmed|min:8|max:60'
        ]);

        $user = $request->user();

        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'message' => 'Invalid Data'
            ]);
        }
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
            'user' => $user
        ]);
    }

    public function delete(Request $request)
    {
        $user_id = $request->user()->id;
        $user = User::where('id', $user_id)->first();

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if ($user->email == $request->email) {
            $request->validate([
                'name' => 'string|max:255',
            ]);

            $user->name = $request->name;
            $user->save();
            return response()->json([
                'message' => 'Updated successfully',
                'user' => $user
            ]);
        } else {

            $request->validate([
                'email' => 'email|unique:users,email',
            ]);

            $user->email = $request->email;

            $user->sendEmailVerificationNotification();

            $user->email_verified_at = null;
            $user->save();

            return response()->json([
                'message' => 'Updated successfully',
                'user' => $user
            ]);
        }
    }

    public function check_if_verified(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at != null) {
            return response()->json([
                'message' => 'verified',
                'verified' => true
            ]);
        }
        return response()->json([
            'message' => 'not verified',
            'verified' => false
        ]);

    }
}
