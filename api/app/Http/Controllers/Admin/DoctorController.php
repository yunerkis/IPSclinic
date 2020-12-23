<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modals\Doctor;
use App\Modals\Schedule;
use Validator;

class DoctorController extends Controller
{
    function __construct()
    {
        // $this->middleware('ApiPermission:doctor.list', ['only' => ['index']]);
        // $this->middleware('ApiPermission:doctor.store', ['only' => ['store']]);
        // $this->middleware('ApiPermission:doctor.show', ['only' => ['show']]);
        // $this->middleware('ApiPermission:doctor.update', ['only' => ['update']]);
        // $this->middleware('ApiPermission:doctor.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $doctors = Doctor::with(['category'])->get();

        return response()->json(['success' => true, 'data' => $doctors], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => [
                'required'
            ],
            'first_names' => [
                'required'
            ],
            'last_names' => [
                'required'
            ],
            'dates' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $doctor = Doctor::create($request->all());

        if ($doctor) {

            foreach ($request['dates'] as $key => $date) {

                Schedule::create([
                    'doctor_id' => $doctor->id,
                    'dayWeeks' => $date['dayWeeks'],
                    'dates' => $date['dates'],
                    'time_start' => $date['time_start'],
                    'time_end' => $date['time_end'],
                    'time' => $date['time'],
                    'availability' => $this->availability($date),
                ]);
            }

            return response()->json(['success' => true, 'data' => 'doctor created'], 201);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }

    public function show($id)
    {
        $doctor = Doctor::find($id);

        if ($doctor) {

            $doctor->with(['category', 'schedules']);

            return response()->json(['success' => true, 'data' => $doctor], 200);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => [
                'required'
            ],
            'first_names' => [
                'required'
            ],
            'last_names' => [
                'required'
            ],
            'dates' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $doctor = Doctor::find($id);

        if ($doctor) {

            $doctor->update($request->all());

            foreach ($request['dates'] as $key => $date) {

                Schedule::updateOrCreate(
                    ['doctor_id' => $doctor->id, 'id' => $date['id']],
                    [
                        'doctor_id' => $doctor->id,
                        'dayWeeks' => $date['dayWeeks'],
                        'dates' => $date['dates'],
                        'time_start' => $date['time_start'],
                        'time_end' => $date['time_end'],
                        'time' => $date['time'],
                        'availability' => $this->availability($date),
                    ]
                );
            }

            return response()->json(['success' => true, 'data' => 'doctor update'], 200);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }

    public function destroy($id)
    {
        $doctor = Doctor::find($id);

        if ($doctor) {

            $doctor->delete();

            return response()->json(['success' => true, 'data' => 'delete'], 200);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }

    private function availability($date)
    {
        $time = explode(':', $date['time']);

        $time = ($time[0]*60) + ($time[1]) + ($time[2]/60).' minutes';

        $interval =  \DateInterval::createFromDateString($time);

        $time1 = new \DateTime($date['time_start']);

        $time2 = new \DateTime($date['time_end']);

        $count = 0;

        while ($time1 < $time2) {
        
            $time1->add($interval);

            $count +=1;
        }

        return $count;
    }
}
