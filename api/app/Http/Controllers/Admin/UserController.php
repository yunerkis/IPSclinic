<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('ApiPermission:users.list', ['only' => ['index']]);
        $this->middleware('ApiPermission:users.destroy', ['only' => ['destroy']]);
    }

    public function index()
    {
        $users = User::where('id', '!=', 1)->get();

        return response()->json(['success' => true, 'data' => $users], 200);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->where('id', '!=', 1)->first(); 

        if ($user) {

            $user->delete();

            return response()->json(['success' => true, 'data' => 'user delete'], 200);
        }

        return response()->json(['success' => false, 'errors' => 'not found'], 404);
    }
}
