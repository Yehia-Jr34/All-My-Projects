<?php

namespace App\Http\Controllers;

use App\Models\Airflight;
use App\Models\AirflightClass;
use App\Models\AirflightUser;
use App\Models\Car;
use App\Models\CarUser;
use App\Models\Flightclass;
use Carbon\Carbon;
use Nnjeim\World\Models\Country;
use Illuminate\Http\Request;

use function PHPUnit\Framework\returnSelf;

class airflightcontroller extends Controller
{
    public function get_state()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'countries',
            'data' => Country::get(),
        ]);
    }
    public function get_airflights(Request $request)
    {
        $statetake = $request->query('statetake');
        $stateland = $request->query('stateland');
        $dated = $request->query('dated');
        $datel = $request->query('datel');
        $flight_class = $request->query('flight_class');
        (int)$passenger = $request->query('passengers');

        // $pass = explode(',', $passenger);
        // for($i = 0 ; $i<=count($pass);$i++)
        // {   
        //     $sum = 0;
        //      $sum += (int)$pass[$i] ;
        // }

        $state_take = Country::where('name', $statetake)->first();
        $state_land = Country::where('name', $stateland)->first();
        $class = Flightclass::where('name', $flight_class)->first()->id;



        $air = AirflightClass::find($class);
        if ($datel == 0) {

            if ($air->exists()) {
                $air->passengers_num += $passenger;
                if ($air->passengers_num > 50) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'the flight class you choose is full',
                    ], 400);
                } else {
                    $Air = Airflight::where('id', $air->airflight_id)
                        ->where('statet_id', $state_take->id)
                        ->where('statel_id', $state_land->id)
                        ->where('active', 1)
                        ->where('departure_datetime', $dated)
                        ->get();
                    // dd();
                }
            } else {

                return response()->json([
                    'status' => 'fail',
                    'message' => 'there is no flights with the class you chose',
                ], 400);
            }
        } else {
            if ($air->exists()) {
                $air->passengers_num += $passenger;
                if (!$air->passengers_num <= 50) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'the flight class you choose is full',
                    ], 400);
                } else {
                    $Air = Airflight::where('id', $air->airflight_id)
                        ->where('statet_id', $state_take->id)
                        ->where('statel_id', $state_land->id)
                        ->where('active', 1)
                        ->whereIn('departure_datetime', [$dated, $datel])
                        ->get();
                }
            } else {

                return response()->json([
                    'status' => 'fail',
                    'message' => 'there is no flights with the class you chose',
                ], 400);
            }
        }
        foreach ($Air as $a) {
            $a['airline_name'] = $a->airline->name;
            $a['IATA'] = $a->airflight_airport->first()->airport->IATA_code;
            unset($a['airflight_airport']);
            unset($a['airline']);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'all flight data',
            'data' => $Air,
        ], 200);
    }

    public function get_car_bill(Request $request)
    {
        $datep = $request->query('date_pick');
        $dater = $request->query('date_return');
        $id = $request->query('id');
        $hotel = Car::where('id', $id)->get();
        $bill = 0;

        $datep = Carbon::createFromFormat('Y-m-d', $datep);
        $dater = Carbon::createFromFormat('Y-m-d', $dater);

        $duration = $dater->diffInDays($datep);

        if ($duration % 2 != 0) {
            $bill = ((int)($duration / 2) * $hotel[0]['room_price']) + $hotel[0]['room_price'];
        } else {
            $bill = (($duration / 2) * $hotel[0]['room_price']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'changed bill ',
            'bill' => $bill
        ], 200);
    }
    public function reserve_airflight(Request $request)
    {

        $user_data = auth()->user()->id;

        $request->validate([
            'airflight_id' => 'required|numeric',
        ]);

        AirflightUser::create([
            'airflight_id' => $request->airflight_id,
            'usermobile_id' => $user_data,
        ]);
        AirflightClass::where('airflight_id', $request->airflight_id)
            ->first()
            ->increment('passengers_num', 1);

        return response()->json([
            'status' => 'success',
            'message' => 'done reservation',
        ], 200);
    }
    public function delete_airflight_reservation(Request $request)
    {
        $user_data = auth()->user()->id;

        $request->validate([
            'airflight_id' => 'required|numeric',
            'flight_class' => 'required',

        ]);
        $id = Flightclass::where('name', $request->flight_class)->first()->id;
        AirflightUser::where('usermobile_id', $user_data)
            ->where('airflight_id', $request->flight_class)
            ->delete();
        AirflightClass::where('flightclass_id', $id)
            ->where('airflight_id', $request->airflight_id)
            ->decrement('passengers_num');

        return response()->json([
            'statue' => 'success',
            'message' => 'done successfuly',
        ], 200);
    }
}
