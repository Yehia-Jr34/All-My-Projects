<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function Laravel\Prompts\password;

class AdminAuthController extends Controller
{
    public function add_admin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:20',
            'email' => [
                'required',
                'email',
                'unique:admins,email'
            ]
        ]);

        $user = $request->user();

        $password = Str::random(12);

        $admin = Admin::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($password),
        ]);

        return response()->json([
            'message' => 'Admin added successfully',
            'added_by' => $user->username,
            'admin' => $admin,
            'password' => $password
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|max:255|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $admin = Admin::get()->where('email', $request->email)->firstOrFail();
            $token = $admin->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login success',
                'doctor' => $admin,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        }
        return response()->json([
            'message' => 'account not found'
        ], 404);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'password' => 'required|confirmed|min:8|max:60'
        ]);

        $admin = $request->user();

        if (!Hash::check($request->oldPassword, $admin->password)) {
            return response()->json([
                'message' => 'Invalid Data'
            ]);
        }
        $admin->update([
            'password' => Hash::make($request->password)
//            'password' => $request->password
        ]);

        return response()->json([
            'message' => 'Password reset successfully',
            'admin' => $admin
        ]);
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
}
