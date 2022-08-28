<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Result;
use App\Models\Client;

class ResultController extends Controller
{
    function __construct()
    {
        $this->middleware('ApiPermission:clients.result', ['only' => ['uploadResult']]);
        $this->middleware('ApiPermission:clients.result.destroy', ['only' => ['destroy']]);
    }

    public function index($dni)
    {
        $client = Client::where('dni', $dni)->first();

        $results = [];

        if ($client) {  

            $results = Result::where('client_id', $client['id'])
                                                ->orderBy('id', 'DESC')->limit(1000)->get();
        }

        return response()->json(['success' => true, 'data' => $results], 200);
    }

    public function uploadResult(Request $request, $id)
    {
        $file =  $request->file('result');

        $extension = $file->getClientOriginalExtension(); 

        $filename = explode('.', $file->getClientOriginalName())[0];
        $filename = str_replace(' ', '_', $filename);
        $filename = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($filename));
        
        $url = 'files/results/'.$filename.'.'.$extension;
        Storage::disk('public')->put($url, File::get($file));
        
        Result::create([
            'client_id' => $id,
            'user_id' => Auth::user()->id,
            'filename' => $filename,
            'url' => $url
        ]);

        return response()->json(['success' => true, 'data' => 'file upload'], 200);
    }

    public function destroy($id)
    {
        $result = Result::where('id', $id)->first(); 

        if ($result) {

            $result->delete();

            return response()->json(['success' => true, 'data' => 'result delete'], 200);
        }

        return response()->json(['success' => false, 'errors' => 'not found'], 404);
    }
}
