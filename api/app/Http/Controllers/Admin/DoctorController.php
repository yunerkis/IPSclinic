<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Schedule;
use Validator;
use Carbon\Carbon;
use App\Models\Session;
use App\Models\User;

class DoctorController extends Controller
{
    function __construct()
    {
        $this->middleware('ApiPermission:doctor.list', ['only' => ['doctors']]);
        $this->middleware('ApiPermission:doctor.store', ['only' => ['store']]);
        $this->middleware('ApiPermission:doctor.show', ['only' => ['show']]);
        $this->middleware('ApiPermission:doctor.update', ['only' => ['update']]);
        $this->middleware('ApiPermission:doctor.delete', ['only' => ['destroy']]);
        $this->middleware('ApiPermission:doctor.user', ['only' => ['doctorUser']]);
    }

    public function index(Request $request)
    {
        date_default_timezone_set('America/Bogota');

        $day = Carbon::today()->format('Y-m-d');

        if ($request['date'] > $day) {
            
            $doctors = Doctor::with(['category', 'schedules'])->whereHas('schedules', function($schedules) use ($request) {
                $schedules->where('dates', 'LIKE', '%'.$request['date'].'%');
            })->get();

        } else {
            
            $doctors = Doctor::with(['category', 'schedules' => function($schedules) use ($request) {
                $schedules->where('dates', 'LIKE', '%'.$request['date'].'%')
                ->where('time_end', '>=', $request['time']);
            }])->whereHas('schedules', function($schedules) use ($request) {
                $schedules->where('dates', 'LIKE', '%'.$request['date'].'%')
                ->where('time_end', '>=', $request['time']);
            })->get();
        }

        $doctors = $doctors->map(function ($doctor) use ($request) {

            $arraySessions = [];

            $sessions = Session::where('date', 'LIKE', '%'.$request['date'].'%')
                                ->where('doctor_id', $doctor['id'])
                                ->select('time')
                                ->get()
                                ->toArray(); 
                                
            foreach ($sessions as $key => $session) {

                $arraySessions[] = $session['time']; 
            }

            $hours = [];

            $times = [];

            foreach ($doctor->schedules as $key => $schedule) {

                $hours[] = date('h:i a', strtotime($schedule['time_start'])).' - '.date('h:i a', strtotime($schedule['time_end']));

                $times[] = $schedule['time_start'].' - '.$schedule['time_end'];

                $schedule['intervals'] = $this->availability($schedule, true, $arraySessions);
            }

            $doctor->hours = $hours;

            $doctor->times = $times;

            return $doctor;
        });

        return response()->json(['success' => true, 'data' => $doctors], 200);
    }

    public function doctors() 
    {
        $doctors = Doctor::with(['category'])->get();

        return response()->json(['success' => true, 'data' => $doctors], 200);
    }

    public function doctorUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required', 'email', 'unique:users'
            ],
            'password' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $user = User::create($request->all());
        $user->assignRole('doctor');

        return response()->json(['success' => false, 'data' => 'User create'], 201);
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

        foreach ($request['dates'] as $key => $date) {

            // $schedules = [];

            // $datesArray = explode(',', $date['dates']);

            // foreach ($datesArray as $key => $dateArray) {

            //     $schedules[$dateArray] = Schedule::where('dates', 'LIKE', '%'.$dateArray.'%')
            //                     ->where(function ($query) use ($date) {
            //                         $query->where('time_start', $date['time_start'])
            //                             ->orWhere('time_end', $date['time_start']);
            //                     })->where(function ($query) use ($date) {
            //                         $query->where('time_start', $date['time_end'])
            //                             ->orWhere('time_end', $date['time_end']);
            //                     })->first();

            //     if ($schedules[$dateArray]) {

            //         $turn = $key == 0 ? 'mañana' : 'tarde';

            //         $doctor->delete();

            //         return response()->json(['success' => false, 'data' => 'Horario '.$turn.' ocupado con la fecha '.$dateArray], 422);
            //     }
            // }

            $count = $this->availability($date);

            if ($count == 0) {

                return response()->json(['success' => false, 'data' => 'Error en el intervalo, verificar las horas'], 422);
            }

            Schedule::create([
                'doctor_id' => $doctor->id,
                'dayWeeks' => $date['dayWeeks'],
                'dates' => $date['dates'],
                'time_start' => $date['time_start'],
                'time_end' => $date['time_end'],
                'time' => $date['time'],
                'availability' => $count,
            ]);
        }

        return response()->json(['success' => true, 'data' => 'doctor created'], 201);
       
    }

    public function show($id)
    {
        $doctor = Doctor::where('id', $id)->with(['category', 'schedules'])->get();

        if ($doctor) {

            $hours = [];

            $times = [];
           
            foreach ($doctor[0]->schedules as $key => $schedule) {

                $hours[] = date('h:i a', strtotime($schedule['time_start'])).' - '.date('h:i a', strtotime($schedule['time_end']));
                
                $schedule['dates'] = explode(',', $schedule['dates']);

                $times[] = $schedule['time_start'].' - '.$schedule['time_end'];
            }

            $doctor[0]->hours = $hours;

            $doctor[0]->times = $times;

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

                    // $schedules = [];

                    // $datesArray = explode(',', $date['dates']);

                    // foreach ($datesArray as $key => $dateArray) {

                    //     $schedules[$dateArray] = Schedule::where('dates', 'LIKE', '%'.$dateArray.'%')
                    //                     ->where('doctor_id', '!=', $doctor->id)
                    //                     ->where(function ($query) use ($date) {
                    //                         $query->where('time_start', $date['time_start'])
                    //                             ->orWhere('time_end', $date['time_start']);
                    //                     })->where(function ($query) use ($date) {
                    //                         $query->where('time_start', $date['time_end'])
                    //                             ->orWhere('time_end', $date['time_end']);
                    //                     })->first();

                    //     if ($schedules[$dateArray]) {

                    //         $turn = $key == 0 ? 'mañana' : 'tarde';

                    //         return response()->json(['success' => false, 'data' => 'Horario '.$turn.' ocupado con la fecha '.$dateArray], 422);
                    //     }
                    // }

                    $count = $this->availability($date);

                    if ($count == 0) {

                        return response()->json(['success' => false, 'data' => 'Error en el intervalo, verificar las horas'], 422);
                    }

                    Schedule::updateOrCreate(
                        ['doctor_id' => $doctor->id, 'id' => $date['id']],
                        [
                            'doctor_id' => $doctor->id,
                            'dayWeeks' => $date['dayWeeks'],
                            'dates' => $date['dates'],
                            'time_start' => $date['time_start'],
                            'time_end' => $date['time_end'],
                            'time' => $date['time'],
                            'availability' => $count,
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

            return response()->json(['success' => true, 'data' => 'doctor delete'], 200);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }

    private function availability($date, $scheduleIntervals = false, $arraySessions = null)
    {
        // $time = explode(':', $date['time']);

        // $time = ($time[0]*60) + ($time[1]) + ($time[2]/60).' minutes';

        $allSchedules = [];

        $time = (string) $date['time'].' minutes';

        $interval =  \DateInterval::createFromDateString($time);

        $time1 = new \DateTime($date['time_start']);

        $time2 = new \DateTime($date['time_end']);

        $count = 0;

        if (isset($arraySessions)) {

            if (!in_array($time1->format('H:i:s'), $arraySessions)) {

                $allSchedules[] = [
                    $time1->format('H:i:s'),
                    $time1->format('h:i a')
                ];
            }   
        }

        while ($time1 < $time2) {

            $time1->add($interval);

            if (isset($arraySessions)) {

                if (!in_array($time1->format('H:i:s'), $arraySessions)) {

                    $allSchedules[] = [
                        $time1->format('H:i:s'),
                        $time1->format('h:i a')
                    ];
                }   
            }
            
            $count +=1;
        }

        if ($count != 0) {

            $count +=1;
        }

        if ($scheduleIntervals) {

            return $allSchedules;
        } else {

            return $count;
        }
    }
}
