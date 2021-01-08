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
        $clients = Client::filter($request)->with(['sessions'])->get();

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

            $attrSession = [
                ['date', '=', $request['date']],
                ['doctor_id', '=', $request['doctor_id']],
                ['time', 'LIKE', '%'.$request['time'].'%'],
            ];

            $session = Session::where($attrSession)->count();
            
            if ($session < $doctor['schedules'][0]->availability) {
                
                $request['client_id'] = $client->id;
               
                $dateNow = Carbon::today()->addDays(2)->format('Y-m-d');
                
                if ($dateNow >= $request['date']) {
                    
                    Session::create($request->all());

                    return response()->json(['success' => true, 'data' => 'Session create'], 201);
                }

                return response()->json(['success' => false, 'data' => 'Session day not correct'], 422);
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
            ->get()
            ->map(function ($session) use ($day, $time){

                $status = true;

                $hours = explode('-', $session->time);
                
                $session->time_start = date('h:i a', strtotime($hours[0])).' - '.date('h:i a', strtotime($hours[1]));

                if ($session->date < $day) {
                   
                    $status = false;
                } elseif ($session->date == $day && date('H:i:s', strtotime($hours[1])) <= $time) {
                    
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

            $session = Session::where($attrSession)->orderBy('id', 'DESC')->first();
           
            if ($session) {

                $hours = explode('-', $session->time);
                
                if (date('H:i', strtotime($hours[1])) >= date('H:i', strtotime($request['time']))) {

                    $session->time_start = date('h:i a', strtotime($hours[0])).' - '.date('h:i a', strtotime($hours[1]));
                } else {
                    
                    $session = NULL;
                }
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
}
