<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
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
        $this->middleware('ApiPermission:clients.sessions.list', ['only' => ['sessionCancel']]);
        $this->middleware('ApiPermission:clients.sessions.imports', ['only' => ['clientImports']]);
    }

    public function index(Request $request)
    {
        $clients = Client::filter($request)->with(['sessions'])->get();

        return response()->json(['success' => true, 'data' => $clients]);
    }

    public function sessions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => [
                'required'
            ],
            'date' => [
                'required'
            ],
            'time_start' => [
                'required'
            ],
            'time_end' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $client = Client::where('dni', $request['dni'])->first();

        if ( $client) {

            $attrSession = [
                ['date', '=', $request['date']],
                ['time_start', '<=', $request['time_end']],
                ['time_end', '>=', $request['time_start']],
            ];
        
            $attrSession2 = [
                ['date', '=', $request['date']],
                ['client_id', '=', $client->id],
            ];

            $session = Session::where($attrSession)->orWhere($attrSession2)->first();

            if (!$session) {

                $request['client_id'] = $client->id;
            
                $dateNow = Carbon::today()->addDays(2)->format('Y-m-d');

                if ($dateNow >= $request['date']) {
                    
                    Session::create($request->all());

                    return response()->json(['success' => true, 'data' => 'Session create'], 201);
                }
            }

            return response()->json(['success' => false, 'data' => 'Session day not correct'], 422);
        }

        return response()->json(['success' => false, 'data' => 'Client not found'], 404);
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

    public function sessionsList($dni)
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

                $session->time_start = date('h:i a', strtotime($session->time_start));
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

    public function schedule(Request $request)
    {  
        $availability = [];
       
        $schedules = [
            ['08:00:00', '09:00:00'],
            ['09:10:00', '10:00:00'],
            ['10:10:00', '11:00:00'],
            ['11:10:00', '12:00:00'],
            ['12:10:00', '13:00:00'],
            ['13:10:00', '14:00:00'],
            ['14:10:00', '15:00:00'],
            ['15:10:00', '16:00:00'],
            ['16:10:00', '17:00:00'],
        ];

        $sessions = Session::where('date', $request->date)->get();
      
        foreach ($schedules as $schedule) {
            
            foreach ($sessions as $session) {
               
                if (!in_array($session->time_start ,$schedule) && !in_array($session->time_end ,$schedule)) {

                    $format = date('h:i a', strtotime($schedule[0])).' - '.date('h:i a', strtotime($schedule[1]));

                    $availability[] = [$format, $schedule];
                }
            }
        }

        if (count($sessions) == 0) {

            foreach ($schedules as $schedule) {

                $format = date('h:i a', strtotime($schedule[0])).' - '.date('h:i a', strtotime($schedule[1]));

                $availability[] = [$format, $schedule];
            }
        }

        return response()->json(['success' => true, 'data' =>  $availability], 200);
    }
}
