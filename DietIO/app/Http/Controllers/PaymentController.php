<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\DoctorWallet;
use App\Models\Payment;
use App\Models\PaymentRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\SubscribingRequest;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $doctor = Doctor::find($request->doctor_id);
        $amount = $request->amount;

        $user_wallet = UserWallet::where('user_id', $user->id)->first();
        $doctor_wallet = DoctorWallet::where('doctor_id', $request->doctor_id)->first();

        // Check if user has enough balance
        if ($user_wallet->value < $amount) {
            return response()->json(['error' => 'Insufficient wallet balance.'], 400);
        }

        // Begin transaction
        DB::transaction(function () use ($user_wallet, $doctor_wallet, $amount, $user, $doctor) {
            // Deduct amount from user's wallet
            $user_wallet->value -= $amount;
            $user_wallet->save();

            // Add amount to doctor's wallet
            $doctor_wallet->value += $amount;
            $doctor_wallet->save();

            // Record the payment
            Payment::create([
                'user_id' => $user->id,
                'doctor_id' => $doctor->id,
                'amount' => $amount,
            ]);
        });

        $reqs = SubscribingRequest::where('user_id', $user->id)->get();

        foreach ($reqs as $reqss) {
            $reqss->status = "cancelled";
            $reqss->save();
        }

        $payment_req = PaymentRequest::where('user_id', $user->id)->where('doctor_id', $doctor->id)->first();

        $payment_req->status = 'Payed';
        $payment_req->save();

        $filePath = 'storage/chats/' . $user->id . '-' . $doctor->id . '-Chat.json';
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'path' => $filePath
        ]);
        
        json_encode([], JSON_PRETTY_PRINT);
        json_decode(Storage::put($filePath, '[]'), false);



        return response()->json([
            'success' => 'Payment successful.',
            'conversation' => $conversation,
            'payment request' => $payment_req
        ], 200);
    }
}
