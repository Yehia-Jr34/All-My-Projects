<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\VerifiedAccount;
use App\Models\Usermobile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|max:55',
    //         'last_name' => 'required|max:55',
    //         'email' => 'email|required|unique:users',
    //         'password' => 'required|confirmed',
    //         'gender' => 'required'
    //     ]);

    //     $validatedData['password'] = bcrypt($request->password);

    //     $user = Usermobile::create($validatedData);

    //     $accessToken = $user->createToken('authToken')->accessToken;

    //     return response(['user' => $user, 'access_token' => $accessToken]);
    // }
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'last_name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
            'gender' => 'required'
        ]);

        /* Generate verified account code */
        $code = rand(100000, 999999);

        $data = [
            'subject' => 'Account Verification',
            'body' => $code
        ];

        try {
            $to = $request->email;

            /* Send email to the user with code */
            Mail::to($to)->send(new VerifiedAccount($data));

            /* Save code into database */
            $user = Usermobile::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'gender' => $request->gender,
                'resetPasswordCode' => 0,
                'verifiedAccountCode' => $code,
            ]);

            $accessToken = $user->createToken('authToken')->accessToken;

            return response(['user' => $user, 'access_token' => $accessToken]);
        } catch (Exception $th) {
            return $th;
            return response()->json([
                "ERROR"
            ], 404);
        }
    }

    public function cancelRegister(Request $request)
    {
        Usermobile::where('email', $request->email)->delete();

        return response()->json([
            "message" => "canceling register done successfully"
        ], 200);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }

    public function profile()
    {
        $user_data = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'User data',
            'data' => $user_data,
        ], 200);
    }

    public function logout(Request $request)
    {

        Auth::guard('api')->user()->token()->revoke();
        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }
}
