<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\Usermobile;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;

class EmailController extends Controller
{
    public function sendResetPasswordEmail(Request $request)
    {
        /* Email validation */
        $request->validate([
            'email' => 'required|email'
        ]);

        /* Check if user's email exists in database */
        $email = Usermobile::where('email', $request->email)->get();

        if (sizeof($email) == 0) {
            return response()->json([
                "This email dose not belong to any user"
            ], 404);
        }

        /* Generate code */
        $code = rand(100000, 999999);

        $data = [
            'subject' => 'Reset Password',
            'body' => $code
        ];

        try {
            $to = $request->email;

            /* Send email to the user with code */
            Mail::to($to)->send(new ResetPassword($data));

            /* Save code into database */
            Usermobile::where('email', $to)->update([
                'resetPasswordCode' => $code
            ]);

            return response()->json([
                "Code have been sent successfully"
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                "ERROR"
            ], 404);
        }
    }

    public function checkResetPasswordCode(Request $request)
    {
        /* Code validation */
        $request->validate([
            'code' => ['required', 'numeric']
        ]);

        /* Check if the code belongs to the same user */
        $userData = Usermobile::where('email', $request->email)->get();

        if ($userData[0]['resetPasswordCode'] == $request->code) {
            return response()->json([
                "Correct code"
            ], 200);
        } else {
            return response()->json([
                "Worng code"
            ], 404);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed'
        ]);

        Usermobile::where('email', $request->email)->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            "Reset password successfully"
        ], 200);
    }

    public function checkEmailVerification(Request $request)
    {
        $request->validate([
            'code' => ['required', 'numeric']
        ]);

        /* Check if the code belongs to the same user */
        $userData = Usermobile::where('email', $request->email)->get();

        if ($userData[0]['verifiedAccountCode'] == $request->code) {
            Usermobile::where('email', $request->email)->update([
                'isVerified' => true
            ]);

            return response()->json([
                "Correct code"
            ], 200);
        } else {
            return response()->json([
                "Worng code"
            ], 404);
        }
    }
}
