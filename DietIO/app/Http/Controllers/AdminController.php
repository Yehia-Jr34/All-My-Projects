<?php

namespace App\Http\Controllers;

use App\Events\AdminAcceptRegistrationRequest;
use App\Events\AdminRejectRegistrationRequest;
use App\Mail\CreateAccountForDoctor;
use App\Models\Doctor;
use App\Models\DoctorWallet;
use App\Models\RegisterationRequests;
use App\Models\User;
use App\Models\ProfilePicture;
use App\Models\CertificationsImages;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function view_registration_requests(Request $request)
{
    $doctors = Doctor::where('status', 'waiting')->get();

    $doctors1 = [];

    foreach ($doctors as $doctor)
    {
        // Fetch the image paths from the database
        $certificate_image_path = CertificationsImages::where('doctor_id', $doctor->id)->first()->path;
        $profile_pic_path = ProfilePicture::where('doctor_id', $doctor->id)->first()->path;

        // Generate the URLs for the images
       // $certificate_image = asset('images/certifications/' . $certificate_image_path);
        //$profile_pic = asset('images/profiles/' . $profile_pic_path);

        // Add the URLs to the doctor object
        $doctor->certificate_image_path = url('/images/certifications/' . $certificate_image_path);
        $doctor->profile_pic_path = url('/images/profiles/' . $profile_pic_path);

        // Add the modified doctor object to the array
        $doctors1[] = $doctor;
    }

    return response()->json([
        'message' => 'done',
        'All requests' => $doctors1
    ]);
}

    public function accept_request(Request $request)
    {
        $user = $request->user(); //admin

        $password = Str::random(12);
        $hashPassword = Hash::make($password);

        $doctor = Doctor::where('id', $request->request_id)->first();
        $doctor->status = 'accepted by ' . $user->username;
        $doctor->password = $hashPassword;
        $doctor->save();

        Mail::to($doctor)->send(new CreateAccountForDoctor($password));

        event(new AdminAcceptRegistrationRequest($user->username));

        $doctor_wallet = DoctorWallet::create([
            'doctor_id' => $doctor->id
        ]);

        return response()->json([
            'message' => 'accepted',
            'doctor' => $doctor,
            'doctor wallet' => $doctor_wallet
        ]);
    }

    public function reject_request(Request $request)
    {
        $user = $request->user();

        $req = Doctor::where('id', $request->request_id)->first();
        $req->status = 'rejected by ' . $user->username;
        $req->save();

        event(new AdminRejectRegistrationRequest($user->username));

        return response()->json([
            'message' => 'reject',
        ]);
    }

    public function getAllUsers(Request $request)
    {
        $users = User::all();
        $doctors = Doctor::all();

        return response()->json([
            'users' => $users,
            'doctors' => $doctors
        ]);
    }

    public function delete_user(Request $request)
    {
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->first();

        $user->delete();

        return response()->json([
            'message' => 'User deleted'
        ]);

    }

    public function search_doctor(Request $request)
    {
        $doctors = Doctor::search($request->search)->get();

        return response()->json([
            'doctors' => $doctors
        ]);
    }

    public function search_user(Request $request)
    {
        $users = User::search($request->search)->get();

        return response()->json([
            'doctors' => $users
        ]);
    }

    public function addMoneyToUserWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($request->user_id);
        $amount = $request->amount;
        
        $wallet = UserWallet::where('user_id', $request->user_id)->first();

        DB::transaction(function () use ($wallet, $user, $amount) {
            $wallet->value += $amount;
            $wallet->save();
            $user->save();
        });

        return response()->json(['success' => 'Money added to user wallet successfully.'], 200);   }

}
