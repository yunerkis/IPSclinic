<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Doctor;
use App\Models\Session;
use Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClientsImport;
use App\Exports\SessionsExport;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    function __construct()
    {
        $this->middleware('ApiPermission:clients.list', ['only' => ['index']]);
        $this->middleware('ApiPermission:clients.sessions.list', ['only' => ['sessionsList', 'sessionCancel']]);
        $this->middleware('ApiPermission:clients.sessions.imports', ['only' => ['clientImports']]);
    }

    public function index(Request $request)
    {
        if (Auth::user()->hasRole('doctor')) {

            $clients = Client::filter($request)->limit(1000)->get();

        } else {
            
            $clients = Client::filter($request)->with(['sessions'])->limit(1000)->get();
        }
        
        return response()->json(['success' => true, 'data' => $clients], 200);
    }

    public function sessions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => [
                'required'
            ],
            'dni' => [
                'required'
            ],
            'date' => [
                'required'
            ],
            'time' => [
                'required'
            ]
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $client = Client::where('dni', $request['dni'])->first();

        $doctor = Doctor::where('id', $request['doctor_id'])->first();

        if ($client && $doctor) {

            $clienteSession = [
                ['client_id', '=', $client->id],
                ['date', '=', $request['date']],
            ];
    
            $session = Session::where($clienteSession)->first();
            
            if ($session && $client->dni != '900219765-2') {
    
                $session->delete();
            }

            $attrSession = [
                ['date', '=', $request['date']],
                ['doctor_id', '=', $request['doctor_id']],
            ];

            $session = Session::where($attrSession)->count();

            $sessionCount = 0;

            foreach ($doctor['schedules'] as $key => $schedule) {

                $scheduleDates = explode(',', $schedule['dates']);

                if (in_array($request['date'], $scheduleDates)) {

                    $sessionCount += $schedule->availability;
                } 
            }

            if ($session < $sessionCount) {

                $attrSession = [
                    ['date', '=', $request['date']],
                    ['doctor_id', '=', $request['doctor_id']],
                    ['time', 'LIKE', '%'.$request['time'].'%'],
                ];

                $session = Session::where($attrSession)->first();

                if (!$session) {

                    $request['client_id'] = $client->id;
                
                    Session::create($request->all());

                    return response()->json(['success' => true, 'data' => 'Session create'], 201);
                }
                
                return response()->json(['success' => false, 'data' => 'Session has created'], 422);          
            }

            return response()->json(['success' => false, 'data' => 'Not availability'], 422);
        }

        return response()->json(['success' => false, 'data' => 'Client or Doctor not found'], 404);
    }

    public function sessionCancel($id)
    {
        $session = Session::find($id);

        if ($session) {

            $session->delete();

            return response()->json(['success' => true, 'data' => 'Session cancel'], 200);
        }

        return response()->json(['success' => false, 'data' => 'Session not found'], 404);
    }

    public function sessionsList()
    {
        date_default_timezone_set('America/Bogota');

        $day = Carbon::today()->format('Y-m-d');

        $time = Carbon::now()->format('H:i:s');
        
        $sessions = Session::orderBy('id', 'DESC')->with(['client', 'doctor'])
            ->limit(1000)
            ->get()
            ->map(function ($session) use ($day, $time){

                $status = true;
                
                $session->time_start = date('h:i a', strtotime($session->time));

                if ($session->date < $day) {
                   
                    $status = false;
                } elseif ($session->date == $day && date('H:i:s', strtotime($session->time)) <= $time) {
                    
                    $status = false;
                }

                $session->status = $status;

                return $session;
            });

        return response()->json(['success' => true, 'data' => $sessions], 200);
    }

    public function sessionsActive($dni, Request $request)
    {   
        date_default_timezone_set('America/Bogota');

        $attrClient = [
            ['dni', '=', $dni],
            ['status', '!=', 'suspendido']
        ];

        $client = Client::where($attrClient)->first();
        
        $session = [];

        if ($client) {

            $attrSession = [
                ['client_id', '=', $client->id],
                ['date', '>=', Carbon::today()->format('Y-m-d')],
            ];

            $session = Session::where($attrSession)->with(['doctor'])->orderBy('id', 'DESC')->first();
            
            if ($session) {

                $session->time_start = date('h:i a', strtotime($session->time));
            } 
        }

        return response()->json(['success' => true, 'data' => ['sessions' => $session, 'client' => $client]], 200);
    }

    public function clientImports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            \Excel::import(new ClientsImport(), $request['file']);

            return response()->json(['success' => true, 'data' => 'Success imports data'], 200);

        } catch (\Exception $e)  {
            
            return response()->json(['success' => false, 'data' => 'error to import data'], 422);
        }   
    }

    public function sessionExport()
    {
        return \Excel::download(new SessionsExport, 'clients.xlsx');
    }
}
