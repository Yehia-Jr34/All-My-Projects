<?php

namespace App\Http\Controllers;

use App\Events\DoctorAcceptConsulationRequest;
use App\Events\DoctorRegectConsulationRequest;
use App\Events\DoctorRequestPayment;
use App\Events\DoctorRequestReview;
use App\Models\Activity;
use App\Models\AllMeals;
use App\Models\AnswerReview;
use App\Models\Diet;
use App\Models\DietDay;
use App\Models\Files;
use App\Models\PaymentRequest;
use App\Models\Review;
use App\Models\SubscribingRequest;
use App\Models\Tag;
use App\Models\TypeOfDiet;
use App\Models\TypeOfMeal;
use App\Models\User;
use App\Mail\DoctorAskForReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DoctorController extends Controller
{
    public function view_request(Request $request)
    {
        $doctor = $request->user();

        $requests = SubscribingRequest::where('doctor_id', $doctor->id)->where('status', 'waiting')->get();

        $object = [];
        $objects = [];

        foreach ($requests as $req) {
            $object ['request'] = $req;

            $user = User::where('id', $req->user_id)->first();
            $object ['user'] = $user;

            $file = Files::where('user_id', $user->id)->first();
            $object ['file'] = $file;

            $target = Tag::where('id', $file->target)->first();
            $activity = Activity::where('id', $file->activity_id)->first();
            $TOD = TypeOfDiet::where('id', $file->type_of_diet_id)->first();

            $object['target'] = $target->tag;
            $object['activity'] = $activity->activity;
            $object['type of diet'] = $TOD->type;

            $objects [] = $object;
        }

        return response()->json([
            'message' => 'done!',
            'objects' => $objects
        ], 200);
    }

    public function accept_request(Request $request)
    {
        $user = $request->user();
        $req = SubscribingRequest::where('doctor_id', $user->id)
            ->where('user_id', $request->user_id)
            ->where('message', $request->message)->first();

        $req->status = "accepted";

        $req->save();

        event(new DoctorAcceptConsulationRequest());

        $request->validate([
            'value' => 'required|numeric'
        ]);

        $pay_req = PaymentRequest::create([
            'user_id' => $request->user_id,
            'doctor_id' => $user->id,
            'value' => $request->value,
            'message' => $request->doctor_message
        ]);

        event(new DoctorRequestPayment());

        return response()->json([
            'message' => 'done!',
            'payment request' => $pay_req
        ]);
    }

    public function cancel_request(Request $request)
    {
        $user = $request->user();
        $req = SubscribingRequest::where('doctor_id', $user->id)
            ->where('user_id', $request->user_id)
            ->where('message', $request->message)->first();

        $req->status = 'Not accepted';

        $req->save();

        event(new DoctorRegectConsulationRequest());

        return response()->json([
            'message' => 'Request not accepted'
        ]);

    }

    public function view_payment_requests(Request $request)
    {
        $user = $request->user();

        $requests = PaymentRequest::where('doctor_id', $user->id)->get();

        return response()->json([
            'message' => 'done',
            'requests' => $requests
        ]);
    }

    public function my_patient(Request $request)
    {
        $user = $request->user();

        $requests = PaymentRequest::where('doctor_id', $user->id)->where('status', 'Payed')->get();

        $users = [];

        foreach ($requests as $request1)
        {
            $user1 = User::where('id', $request1->user_id)->first();
            $file = Files::where('user_id', $request1->user_id)->first();

            $target = Tag::where('id', $file->target)->first();
            $activity = Activity::where('id', $file->activity_id)->first();
            $TOD = TypeOfDiet::where('id', $file->type_of_diet_id)->first();

            $file->target = $target->tag;
            $file->activity = $activity->activity;
            $file->type_of_diet = $TOD->type;

            $user1->file = $file;

            $users [] = $user1;
        }

        return response()->json([
            'message' => 'done',
            'patient' => $users
        ]);
    }

    public function view_user_file(Request $request)
    {
        $user = $request->user();

        $file = Files::where('user_id',$request->user_id)->first();

        $target = Tag::where('id', $file->target)->first();
        $tod = TypeOfDiet::where('id', $file->type_of_diet_id)->first();
        $activity = Activity::where('id', $file->activity_id)->first();

        $file->target = $target->tag;
        $file->activity = $activity->activity;
        $file->type_of_diet = $tod->type;

        return response()->json([
            'message' => 'done!',
            'file' => $file,
        ]);
    }

    public function create_diet(Request $request)
    {
        $doctor = $request->user();

        $user = User::where('id', $request->user_id)->first();

        $request->validate([
            'user_id' => 'required',
            'duration' => 'required|numeric',
            'review_frequency' => 'required|numeric'
        ]);

        $file = Files::where('user_id', $user->id)->first();

        $diet = Diet::create([
            'duration' => $request->duration,
            //'tag_id' => $file->target,
            'doctor_id' => $doctor->id,
            'user_id' => $user->id,
            'review_frequency' => $request->review_frequency,
        ]);

        return response()->json([
            'Message' => 'New diet added',
            'Doctor' => $doctor,
            'User' => $user,
            'User file' => $file,
            'Diet' => $diet
        ]);

    }

    public function add_diet_day(Request $request)
    {
        $doctor = $request->user();

        $request->validate([
            'day_number' => 'required|numeric',
            'week_number' => 'required|numeric',
        ]);

        $day_number = $request->day_number + 1;
        $diet = Diet::where('id', $request->diet_id)->first();
        $day = [];

        foreach ($request->meals as $meal) {
            // Ensure $meal is treated as an array
            $meall = AllMeals::create([
                'name' => $meal['name'], // Use array syntax
                'meal_time' => $meal['time'] // Use array syntax
            ]);
           
            $meal_type = TypeOfMeal::where('type', $meal['meal_type'])->first();
            $diet_day = DietDay::create([
                'day_number' => $day_number,
                'week_number' => $request->week_number,
                'meal_id' => $meall->id,
                'diet_id' => $request->diet_id,
                'meal_type_id' => $meal_type->id,
            ]);
            $day[] = $diet_day;
        }

        return response()->json([
            'message' => 'New day added',
            'day_number' => $day_number,
            'day' => $day
        ]);
    }

    public function show_diet(Request $request)
    {
        $user = $request->user();

        $diets = Diet::where('doctor_id', $user->id)->where('user_id',$request->user_id)->first();
        $dietDays = DietDay::orderBy('week_number', 'asc')
        ->orderBy('day_number', 'asc')
        ->get();

    $dietStructure = [];
    

    foreach ($dietDays as $dietDay) {
        $weekNumber = $dietDay->week_number;
        $dayNumber = $dietDay->day_number;

        $mealTypeData = TypeOfMeal::where('id', $dietDay->meal_type_id)->first();
        $mealData = AllMeals::where('id', $dietDay->meal_id)->first();

        $dietDay->meal = $mealData->name;
        $dietDay->meal_type = $mealTypeData->type;
        $dietDay->time= $mealData->meal_time;

        $review = Review::where('week_number',$dietDay->week_number)->where('diet_id', $diets->id)->first();
        $answer_review = AnswerReview::where('review_id', $review->id)->first();

        if ($answer_review != null)
        {
            if (!isset($dietStructure[$weekNumber])) {
                $dietStructure[$weekNumber] = [
                    'week_number' => $weekNumber,
                    'days' => [],
                    'review' => $review->review,
                    'had_answer' => true
                ];
            }
        } else {
            if (!isset($dietStructure[$weekNumber])) {
                $dietStructure[$weekNumber] = [
                    'week_number' => $weekNumber,
                    'days' => [],
                    'review' => $review->review,
                    'had_answer' => false
                ];
            }
        }

        if (!isset($dietStructure[$weekNumber]['days'][$dayNumber])) {
            $dietStructure[$weekNumber]['days'][$dayNumber] = [
                'day_number' => $dayNumber,
                'meals' => [],
                
            ];
        }

        $dietStructure[$weekNumber]['days'][$dayNumber]['meals'][] = $dietDay;
    }

    $dietStructure = array_values($dietStructure);

    foreach ($dietStructure as &$week) {
        $week['days'] = array_values($week['days']);
    }

        return response()->json([
            'message' => 'done!',
            'diet' => $diets,
            'number of weeks' => count($dietStructure),
            'weeks' => $dietStructure,
        ]);
    }

    public function get_answer(Request $request)
    {
        $review = Review::where('diet_id', $request->diet_id)->where('week_number', $request->week_number)->first();

        $answer_review = AnswerReview::where('review_id', $review->id)->first();

        if ($answer_review != null)
        {
            if ($answer_review->image_path != null) 
            {
                return response()->json([
                    'message' => 'done with image',
                    'answer' => $answer_review->answer,
                    'has_image' => true,
                    'image' => url('images/reviews/'. $answer_review->image_path)
                ]);
            } else {
                return response()->json([
                    'message' => 'done without image',
                    'answer' => $answer_review->answer,
                    'has_image' => false,
                ]);
            }
        } else {
            return response()->json([
                'message' => 'fail',
            ]);
        }
    }

    public function request_review(Request $request)
    {
        $user = $request->user();

        $review = Review::create([
            'doctor_id' => $user->id,
            'user_id' => $request->user_id,
            'review' => $request->review,
            'week_number' => $request->week_number,
            'diet_id' => $request->diet_id,
        ]);

        // event(new DoctorRequestReview($request->review));

        $user1 = User::where('id', $request->user_id)->first();

        Mail::to($user1->email)->send(new DoctorAskForReview($user1->name,$request->week_number,$user->firstName. ' ' . $user->lastName));

        return response()->json([
            'message' => 'Request sent'
        ]);
    }

    public function view_reviews_response(Request $request)
    {
        $user = $request->user();

        $reviews = Review::where('doctor_id', $user->id)->get();

        $full_reviews = [];

        foreach ($reviews as $review) {
            $rev = [];
            $rev [] = $review;

            $patient = User::where('id', $review->user_id);

            $ans_rev = AnswerReview::where('review_id', $review->id)->first();

            $rev [] = $ans_rev;

            $rev [] = $patient;

            $full_reviews [] = $rev;
        }

        return response()->json([
            'reviews' => $full_reviews
        ]);
    }
}
