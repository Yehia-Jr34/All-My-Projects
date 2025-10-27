<?php

namespace App\Http\Controllers\API;

use App\Events\DoctorRequestToJoin;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordForDoctor;
use App\Models\CertificationsImages;
use App\Models\Doctor;
use App\Models\ProfilePicture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class DoctorAuthController extends Controller
{
    public function registrationRequest(Request $request)
    {
        // Validate the request
        $request->validate([
            'firstName' => 'required|string|min:3|max:20',
            'lastName' => 'required|string|min:3|max:20',
            'email' => 'required|email|unique:doctors,email',
            'phoneNumber' => 'required|numeric|digits_between:0,9',
            'profile_photo' => 'nullable|string',
            'certification_photo' => 'nullable|string',
        ]);

        $doctor = Doctor::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'phoneNumber' => $request->phoneNumber,
        ]);

        $certification_image = $request->certification_image;
        $ext = $certification_image->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $certification_image->move(public_path() . '/images/certifications', $imageName);

        // Save the path in the database
        $image1 = CertificationsImages::create([
            'doctor_id' => $doctor->id,
            'path' => $imageName
        ]);

        $profile_pic = $request->profile_picture;
        $ext = $profile_pic->getClientOriginalExtension();
        $imageName2 = time() . '.' . $ext;
        $profile_pic->move(public_path() . '/images/profiles', $imageName);

        // Save the path in the database
        $image2 = ProfilePicture::create([
            'doctor_id' => $doctor->id,
            'path' => $imageName2
        ]);

        $doctor->certificate_image_path = url('images/certification/' . $image1->path);
        $doctor->profile_picture = url('images/profiles/' . $image2->path);

        event(new DoctorRequestToJoin($request->firstName . ' ' . $request->lastName, $doctor->id));

        return response()->json([
            'message' => 'Request sent successfully',
            'request' => $doctor,
            'role' => 'doctor',
        ]);
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|string|max:255|email',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     if (Auth::guard('doctor')->attempt(['email' => $request->email, 'password' => $request->password])) {
    //         $doctor = Doctor::get()->where('email', $request->email)->firstOrFail();
    //         $token = $doctor->createToken('auth_token')->plainTextToken;
    //         return response()->json([
    //             'message' => 'Login success',
    //             'doctor' => $doctor,
    //             'access_token' => $token,
    //             'token_type' => 'Bearer',
    //             'role' => 'doctor',

    //         ], 200);
    //     }
    //     return response()->json([
    //         'message' => 'account not found'
    //     ], 404);
    // }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|max:255|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::guard('doctor')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $doctor = Doctor::get()->where('email', $request->email)->firstOrFail();
            $token = $doctor->createToken('auth_token')->plainTextToken;

            $certificate_image = CertificationsImages::where('doctor_id', $doctor->id)->first();
            $profile_pic = ProfilePicture::where('doctor_id', $doctor->id)->first();


            $doctor->certificate_image_path = url('/images/certifications/'.$certificate_image->path);
            $doctor->profile_pic_path = url('/images/profiles/'.$profile_pic->path);
            
            return response()->json([
                'message' => 'Login success',
                'doctor' => $doctor,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => 'doctor',
            ], 200);
        }
        return response()->json([
            'message' => 'account not found'
        ], 404);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        //images
        $certificate_image = CertificationsImages::where('doctor_id', $user->id)->first();
        $certificate_image_path = $certificate_image->path;

        $profile_pic = ProfilePicture::where('doctor_id', $user->id)->first();
        $profile_pic_path = $profile_pic->path;

        $user->certificate_image_path = 'storge/public/' . $certificate_image_path;
        $user->profile_pic_path = 'storge/public/' . $profile_pic_path;

        return response()->json([
            'user' => $user,
        ], 200);
    }

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
//            'password' => $request->password
        ]);

        return response()->json([
            'message' => 'Password reset successfully',
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {

        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('doctors')->ignore(\auth()->user()->id)],
            'phoneNumber' => 'string|max:255',
        ]);

        $user = auth()->user();

        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->email = $request->email;
        $user->phoneNumber = $request->phoneNumber;

        $user->save();

        if ($request->certification_image != null) {
            $cer_img = CertificationsImages::where('doctor_id', $user->id)->first();
            $path1 = $request->file('certification_image')->store('images/certifications', 'public');

            if ($path1 != null) {
                $cer_img->path = 'storge/public/' . $path1;
                $cer_img->save();
            } else {
                $path1 = $cer_img->path;
            }
        } if ($request->profile_picture != null) {
            $profile_pic = ProfilePicture::where('doctor_id', $user->id)->first();
            $path2 = $request->file('profile_picture')->store('images/profiles', 'public');

            if ($path2 != null) {
                $profile_pic->path = 'storge/public/' . $path2;
                $profile_pic->save();
            } else {
                $path2 = $profile_pic->path;
            }
        }

        $certificate_image_path = CertificationsImages::where('doctor_id', $user->id)->first()->path;
        $profile_pic_path = ProfilePicture::where('doctor_id', $user->id)->first()->path;


        $user->certificate_image_path = 'storge/public/' . $certificate_image_path;
        $user->profile_pic_path = 'storge/public/' . $profile_pic_path;

        return response()->json([
            'message' => 'Updated successfully',
            'user' => $user,
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

    public function delete()
    {
        $user = auth()->user();
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function showLinkRequestForm()
    {
        return view('auth.doctor-forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            $doctor = Doctor::where('email', $request->email)->first();
            Mail::to($doctor)->send(new ResetPasswordForDoctor($doctor));
            return back()->with('status', __($status));
        }
        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.doctor-reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $doctor = Doctor::where('email', $request->email)->first();

        $request->only('email', 'password', 'password_confirmation', 'token');

        if ($doctor) {
            $doctor->password = $request->password;

            $doctor->save();
            return response()->json([
                'message' => 'Password updated',
                'user' => $doctor
            ]);
        }

        return response()->json([
            'message' => 'Error : Doctor account not found'
        ]);
    }
}
