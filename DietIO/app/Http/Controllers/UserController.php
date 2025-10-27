<?php

namespace App\Http\Controllers;

use App\Events\UserRequestConsulation;
use App\Models\Activity;
use App\Models\AnswerReview;
use App\Models\Diet;
use App\Models\Doctor;
use App\Models\FavDoctor;
use App\Models\Files;
use App\Models\PaymentRequest;
use App\Models\rateDoctor;
use App\Models\Review;
use App\Models\DietDay;
use App\Models\SubscribingRequest;
use App\Models\AllMeals;
use App\Models\TypeOfMeal;
use App\Models\Tag;
use App\Models\TypeOfDiet;
use App\Models\UserImage;
use Illuminate\Http\Request;
use App\Models\ProfilePicture;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserAnsweredTheReview;
use App\Models\CertificationsImages;

class UserController extends Controller
{
    public function create_file(Request $request)
    {
        $request->validate([
            'weight' => 'required|string',
            'height' => 'required',
            'age' => 'required|numeric',
            'gender' => 'required',
            'waistline' => 'required|numeric|min:20|max:80',
            'buttocks_cir' => 'required|numeric|min:20|max:80',
            'target' => 'required|numeric|min:0|max:5',
            'number_of_meals' => 'required|min:0|max:5',
            'activity_id' => 'required|numeric',
            'type_of_diet_id' => 'required|numeric',
            'diseases' => 'required',
            'surgery' => 'required',
            'wake_up' => 'required',
            'sleep' => 'required',
            'job' => 'required|string',
            'study' => 'required|string',
            'daily_routine' => 'required',

        ]);

        $user = $request->user();

        $file = Files::create([
            'user_id' => $user->id,
            'weight' => $request->weight,
            'height' => $request->height,
            'age' => $request->age,
            'gender' => $request->gender,
            'waistline' => $request->waistline,
            'buttocks_cir' => $request->buttocks_cir,
            'target' => $request->target,
            'number_of_meals' => $request->number_of_meals,
            'activity_id' => $request->activity_id,
            'type_of_diet_id' => $request->type_of_diet_id,
            'diseases' => $request->diseases,
            'surgery' => $request->surgery,
            'wake_up' => $request->wake_up,
            'sleep' => $request->sleep,
            'job' => $request->job,
            'study' => $request->study,
            'daily_routine' => $request->daily_routine,
            'notes' => $request->notes,
        ]);

        $target = Tag::where('id', $file->target)->first();
        $activity = Activity::where('id', $file->activity_id)->first();
        $TOD = TypeOfDiet::where('id', $file->type_of_diet_id)->first();


        return response()->json([
            'message' => 'File created successfully',
            'user' => $user,
            'file' => $file,
            'Target' => $target->tag,
            'Activity' => $activity->activity,
            'Type of diet' => $TOD->type
        ]);

    }

    public function update_file(Request $request)
    {
        $request->validate([
            'weight' => 'required|string',
            'height' => 'required|string',
            'age' => 'required|numeric',
            'gender' => 'required',
            'waistline' => 'required|numeric|min:20|max:80',
            'buttocks_cir' => 'required|numeric|min:20|max:80',
            'target' => 'required|numeric|min:0|max:5',
            'number_of_meals' => 'required|min:0|max:5',
            'activity_id' => 'required|numeric',
            'type_of_diet_id' => 'required|numeric',
            'diseases' => 'required',
            'surgery' => 'required',
            'wake_up' => 'required',
            'sleep' => 'required',
            'job' => 'required|string',
            'study' => 'required|string',
            'daily_routine' => 'required',
        ]);

        $user = $request->user();

        $file = Files::where('user_id', $user->id)->first();
        $user = $file->user;

        $file->weight = $request->weight;
        $file->height = $request->height;
        $file->age = $request->age;
        $file->gender = $request->gender;
        $file->waistline = $request->waistline;
        $file->buttocks_cir = $request->buttocks_cir;
        $file->target = $request->target;
        $file->number_of_meals = $request->number_of_meals;
        $file->activity_id = $request->activity_id;
        $file->type_of_diet_id = $request->type_of_diet_id;
        $file->diseases = $request->diseases;
        $file->surgery = $request->surgery;
        $file->wake_up = $request->wake_up;
        $file->sleep = $request->sleep;
        $file->job = $request->job;
        $file->study = $request->study;
        $file->daily_routine = $request->daily_routine;
        $file->notes = $request->notes;

        $file->save();

        $target = Tag::where('id', $file->target)->first();
        $activity = Activity::where('id', $file->activity_id)->first();
        $TOD = TypeOfDiet::where('id', $file->type_of_diet_id)->first();

        return response()->json([
            'message' => 'File updated successfully',
            'user' => $user,
            'file' => $file,
            'Target' => $target->tag,
            'Activity' => $activity->activity,
            'Type of diet' => $TOD->type
        ]);
    }

    public function view_file(Request $request)
    {
        $user = $request->user();

        $file = Files::where('user_id', $user->id)->first();

        $target = Tag::where('id', $file->target)->first();
        $activity = Activity::where('id', $file->activity_id)->first();
        $TOD = TypeOfDiet::where('id', $file->type_of_diet_id)->first();

        return response()->json([
            'File' => $file,
            'Target' => $target->tag,
            'Activity' => $activity->activity,
            'Type of diet' => $TOD->type
        ]);

    }

    public function view_doctors(Request $request)
    {
        $user = $request->user();
        $doctors = Doctor::all();

        $doctors1 = [];
        $i = 1;

        foreach ($doctors as $doctor) {
            $d = FavDoctor::where('doctor_id', $doctor->id)->where('user_id', $user->id)->first();

            if ($d != null) {
                $doctor->fav = true;
                $doctors1 [] = $doctor;
            } else {
                $doctor->fav = false;
                $doctors1 [] = $doctor;
            }
            $i++;
        }

        return response()->json([
            'message' => 'done',
            'favorite doctors' => $doctors1
        ]);
    }


    public function add_doctor_to_fav(Request $request)
    {
        $user = $request->user();
        FavDoctor::create([
            'user_id' => $user->id,
            'doctor_id' => $request->doctor_id
        ]);

        return response()->json([
            'message' => 'Doctor added to favorite'
        ]);
    }

    public function view_my_favorite(Request $request)
    {
        $user = $request->user();
        $favorites = FavDoctor::where('user_id', $user->id)->get();
        $doctors = [];
        foreach ($favorites as $favorite) {
            $doctor = Doctor::where('id', $favorite->doctor_id)->first();
            $doctors[] = $doctor;
        }

        return response()->json([
            'message' => 'done',
            'doctors' => $doctors,
        ]);
    }



    public function rete_doctor(Request $request)
    {
        $user = $request->user();

        $rate = rateDoctor::create([
            'user_id' => $user->id,
            'doctor_id' => $request->doctor_id,
            'rate' => $request->rate,
        ]);

        $doctor = Doctor::where('id', $request->doctor_id)->first();

        // $doctor->number_of_rates = $doctor->number_of_rates + 1;
        // $doctor->rate = ($doctor->rate + $request->rate) / $doctor->number_of_rates;

        // $doctor->save();

        return response()->json([
            'message' => 'Rate added',
            'rate' => $rate
        ]);
    }

    public function view_my_rates(Request $request)
    {
        $user = $request->user();

        $rates = rateDoctor::where('user_id', $user->id)->get();

        return response()->json([
            'rates' => $rates
        ]);
    }

    public function consultation_request(Request $request)
    {
        $user = $request->user();


        $sub_req = SubscribingRequest::create([
            'user_id' => $user->id,
            'doctor_id' => $request->doctor_id,
            'message' => $request->message
        ]);

        event(new UserRequestConsulation($user->name));

        return response()->json([
            'message' => 'request sent',
            'consultation request' => $sub_req
        ]);
    }

    public function view_my_requests(Request $request)
    {
        $user = $request->user();

        $sub_requests = SubscribingRequest::where('user_id', $user->id)->get();
        $pay_requests = PaymentRequest::where('user_id', $user->id)->get();

        $array = [];

        foreach ($sub_requests as $req) {
            $doctor = Doctor::where('id', $req->doctor_id)->first();
            $req->doctor_id = $doctor;
            array_push($array, $req);
        }

        $pay_array = [];

        foreach ($pay_requests as $req) {
            $doctor = Doctor::where('id', $req->doctor_id)->first();
            $req->doctor_id = $doctor;
            array_push($pay_array, $req);
        }

        return response()->json([
            'message' => 'done!',
            'subsicribe requests' => $array,
            'payment requests' => $pay_array
        ]);
    }

    public function cancel_request(Request $request)
    {
        $user = $request->user();

        $req = SubscribingRequest::where('id', $request->request_id)->first();

        $req->status = "Canceled";

        $req->save();

        return response()->json([
            'message' => 'canceled',
            'user' => $user,
            'request' => $req
        ]);
    }

    public function view_payment_requests(Request $request)
    {
        $user = $request->user();

        $requests = PaymentRequest::where('user_id', $user->id)->get();

        return response()->json([
            'message' => 'done',
            'requests' => $requests
        ]);
    }
     



    public function view_my_diets(Request $request)
    {
        $user = $request->user();

        $diets = Diet::where('user_id', $user->id)->first();

        if ($diets != null) {
            $dietDays = DietDay::orderBy('week_number', 'asc')
                ->orderBy('day_number', 'asc')
                ->get();

            $dietStructure = [];

            $doctor = Doctor::where('id', $diets->doctor_id)->first();

            $certificate_image = CertificationsImages::where('doctor_id', $doctor->id)->first();
            $certificate_image_path = $certificate_image->path;

            $profile_pic = ProfilePicture::where('doctor_id', $doctor->id)->first();
            $profile_pic_path = $profile_pic->path;

            $doctor->certificate_image_path = 'storge/public/' . $certificate_image_path;
            $doctor->profile_pic_path = 'storge/public/' . $profile_pic_path;

            foreach ($dietDays as $dietDay) {
                $weekNumber = $dietDay->week_number;
                $dayNumber = $dietDay->day_number;

                $mealTypeData = TypeOfMeal::where('id', $dietDay->meal_type_id)->first();
                $mealData = AllMeals::where('id', $dietDay->meal_id)->first();

                $dietDay->meal = $mealData->name;
                $dietDay->meal_type = $mealTypeData->type;
                $dietDay->time = $mealData->meal_time;

                $review = Review::where('week_number', $dietDay->week_number)->where('diet_id', $diets->id)->first();
                $answer_review = AnswerReview::where('review_id', $diets->id)->first();


                $review = Review::where('week_number', $dietDay->week_number)->where('diet_id', $diets->id)->first();
                $answer_review = AnswerReview::where('review_id', $review->id)->first();

                if ($answer_review != null) {
                    if (!isset($dietStructure[$weekNumber])) {
                        $dietStructure[$weekNumber] = [
                            'week_number' => $weekNumber,
                            'days' => [],
                            'review_id' => $review->id,
                            'review' => $review->review,
                            'had_answer' => true
                        ];
                    }
                } else {
                    if (!isset($dietStructure[$weekNumber])) {
                        $dietStructure[$weekNumber] = [
                            'week_number' => $weekNumber,
                            'days' => [],
                            'review_id' => $review->id,
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
                'doctor' => $doctor,
            ]);
        } else {
            return response()->json([
                'message' => 'There is no diet for you',
//                'doctor' => $doctor,
            ]);
        }
    }

    // public function view_my_diets(Request $request)
    // {
    //     $user = $request->user();

    //     $diets = Diet::where('user_id', $user->id)->first();
    //     $dietDays = DietDay::orderBy('week_number', 'asc')
    //         ->orderBy('day_number', 'asc')
    //         ->get();

    //     $dietStructure = [];

    //     $doctor = Doctor::where('id', $diets->doctor_id)->first();

    //     $certificate_image = CertificationsImages::where('doctor_id', $doctor->id)->first();
    //     $certificate_image_path = $certificate_image->path;

    //     $profile_pic = ProfilePicture::where('doctor_id', $doctor->id)->first();
    //     $profile_pic_path = $profile_pic->path;

    //     $doctor->certificate_image_path = 'storge/public/' . $certificate_image_path;
    //     $doctor->profile_pic_path = 'storge/public/' . $profile_pic_path;

    //     foreach ($dietDays as $dietDay) {
    //         $weekNumber = $dietDay->week_number;
    //         $dayNumber = $dietDay->day_number;

    //         $mealTypeData = TypeOfMeal::where('id', $dietDay->meal_type_id)->first();
    //         $mealData = AllMeals::where('id', $dietDay->meal_id)->first();

    //         $dietDay->meal = $mealData->name;
    //         $dietDay->meal_type = $mealTypeData->type;
    //         $dietDay->time= $mealData->meal_time;

    //         $review = Review::where('week_number', $dietDay->week_number)->where('diet_id', $diets->id)->first();
    //         $answer_review = AnswerReview::where('review_id', $diets->id)->first();

           
    //         $review = Review::where('week_number', $dietDay->week_number)->where('diet_id', $diets->id)->first();
    //         $answer_review = AnswerReview::where('review_id', $review->id)->first();

    //         if ($answer_review != null) {
    //             if (!isset($dietStructure[$weekNumber])) {
    //                 $dietStructure[$weekNumber] = [
    //                     'week_number' => $weekNumber,
    //                     'days' => [],
    //                     'review_id' => $review->id,
    //                     'review' => $review->review,
    //                     'had_answer' => true
    //                 ];
    //             }
    //         } else {
    //             if (!isset($dietStructure[$weekNumber])) {
    //                 $dietStructure[$weekNumber] = [
    //                     'week_number' => $weekNumber,
    //                     'days' => [],
    //                     'review_id' => $review->id,
    //                     'review' => $review->review,
    //                     'had_answer' => false
    //                 ];
    //             }
    //         }

    //         if (!isset($dietStructure[$weekNumber]['days'][$dayNumber])) {
    //             $dietStructure[$weekNumber]['days'][$dayNumber] = [
    //                 'day_number' => $dayNumber,
    //                 'meals' => [],

    //             ];
    //         }

    //         $dietStructure[$weekNumber]['days'][$dayNumber]['meals'][] = $dietDay;
    //     }

    //     $dietStructure = array_values($dietStructure);
    //     foreach ($dietStructure as &$week) {
    //         $week['days'] = array_values($week['days']);
    //     }

    //     return response()->json([
    //         'message' => 'done!',
    //         'diet' => $diets,
    //         'number of weeks' => count($dietStructure),
    //         'weeks' => $dietStructure,
    //         'doctor' => $doctor,
    //     ]);
    // }


    // public function view_my_diets(Request $request)
    // {
    //     $user = $request->user();

    //     $diets = Diet::where('user_id', $user->id)->first();
    //     $dietDays = DietDay::orderBy('week_number', 'asc')
    //     ->orderBy('day_number', 'asc')
    //     ->get();

    //  $dietStructure = [];

    //  foreach ($dietDays as $dietDay) {
    //     $weekNumber = $dietDay->week_number;
    //     $dayNumber = $dietDay->day_number;

    //     $mealTypeData = TypeOfMeal::where('id', $dietDay->meal_type_id)->first();
    //     $mealData = AllMeals::where('id', $dietDay->meal_id)->first();

    //     $dietDay->meal = $mealData->name;
    //     $dietDay->meal_type = $mealTypeData->type;

    //     $review = Review::where('week_number',$dietDay->week_number)->where('diet_id', $diets->id)->first();
    //     $answer_review = AnswerReview::where('review_id', $diets->id)->first();

    // //     if (!isset($dietStructure[$weekNumber])) {
    // //         $dietStructure[$weekNumber] = [
    // //             'week_number' => $weekNumber,
    // //             'days' => [],
    // //             'review' => $review,
    // //             'answer review' => $answer_review,
    // //         ];
    // //     }

    // //     if (!isset($dietStructure[$weekNumber]['days'][$dayNumber])) {
    // //         $dietStructure[$weekNumber]['days'][$dayNumber] = [
    // //             'day_number' => $dayNumber,
    // //             'meals' => [],
                
    // //         ];
    // //     }

    // //     $dietStructure[$weekNumber]['days'][$dayNumber]['meals'][] = $dietDay;
    // // }

    

    // // $dietStructure = array_values($dietStructure);

    // // foreach ($dietStructure as &$week) {
    // //     $week['days'] = array_values($week['days']);
    // // }

    // //     return response()->json([
    // //         'message' => 'done!',
    // //         'diet' => $diets,
    // //         'number of weeks' => count($dietStructure),
    // //         'weeks' => $dietStructure,
    // //     ]);
    
    // // }
    // $review = Review::where('week_number',$dietDay->week_number)->where('diet_id', $diets->id)->first();
    //     $answer_review = AnswerReview::where('review_id', $review->id)->first();

    //     if ($answer_review != null)
    //     {
    //         if (!isset($dietStructure[$weekNumber])) {
    //             $dietStructure[$weekNumber] = [
    //                 'week_number' => $weekNumber,
    //                 'days' => [],
    //                 'review' => $review->review,
    //                 'had_answer' => true
    //             ];
    //         }
    //     } else {
    //         if (!isset($dietStructure[$weekNumber])) {
    //             $dietStructure[$weekNumber] = [
    //                 'week_number' => $weekNumber,
    //                 'days' => [],
    //                 'reviewanswerReview' => $review->review,
    //                 'had_answer' => false
    //             ];
    //         }
    //     }

    //     if (!isset($dietStructure[$weekNumber]['days'][$dayNumber])) {
    //         $dietStructure[$weekNumber]['days'][$dayNumber] = [
    //             'day_number' => $dayNumber,
    //             'meals' => [],
                
    //         ];
    //     }

    //     $dietStructure[$weekNumber]['days'][$dayNumber]['meals'][] = $dietDay;
    // }

    // $dietStructure = array_values($dietStructure);

    // foreach ($dietStructure as &$week) {
    //     $week['days'] = array_values($week['days']);
    // }

    //     return response()->json([
    //         'message' => 'done!',
    //         'diet' => $diets,
    //         'number of weeks' => count($dietStructure),
    //         'weeks' => $dietStructure,
    //     ]);
    // }

    public function delete_fav(Request $request)
    {
        $user = $request->user();

        FavDoctor::where('user_id', $user->id)->where('doctor_id', $request->doctor_id)->delete();

        return response()->json([
            'message' => 'deleted'
        ]);
    }

    public function answer_review(Request $request)
    {
        $user = $request->user();

        $answer_review = AnswerReview::create([
            'answer' => $request->answer,
            'review_id' => $request->review_id
        ]);

        $review = Review::where('id',$request->review_id)->first();

        $doctor = Doctor::where('id', $review->doctor_id)->first();

        if ($request->image != null) {
            $answer_image = $request->image;
            $ext = $answer_image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $answer_image->move(public_path() . '/images/reviews/', $imageName);

            // Save the path in the database
            $answer_review->image_path = $imageName;
            $answer_review->save();

            $answer_review->image = url('images/reviews/' . $answer_review->image_path);
        }

        Mail::to($doctor->email)->send(new UserAnsweredTheReview($user->name,$review->week_number,$doctor->firstName. ' ' . $user->lastName));

        return response()->json([
            'message' => 'Review answered',
            'answer' => $answer_review,
        ]);
    }

    public function search_doctor(Request $request)
    {
        $doctors = Doctor::search($request->search)->get();

        return response()->json([
            'doctors' => $doctors
        ]);
    }

}
