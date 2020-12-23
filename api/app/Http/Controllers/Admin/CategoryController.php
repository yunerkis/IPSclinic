<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Validator;

class CategoryController extends Controller
{
    function __construct()
    {
        $this->middleware('ApiPermission:category.list', ['only' => ['index']]);
        $this->middleware('ApiPermission:category.store', ['only' => ['store']]);
        $this->middleware('ApiPermission:category.update', ['only' => ['update']]);
        $this->middleware('ApiPermission:category.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $categories = Category::all();

        return response()->json(['success' => true, 'data' => $categories], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        Category::create($request->all());

        return response()->json(['success' => true, 'data' => 'category created'], 201);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if ($category) {

            return response()->json(['success' => true, 'data' => $category], 200);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required'
            ],
        ]);

        if ($validator->fails()) {

        	return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $category = Category::find($id);

        if ($category) {

            $category->update($request->all());

            return response()->json(['success' => true, 'data' => $category], 200);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category) {

            $category->delete();

            return response()->json(['success' => true, 'data' => 'delete'], 200);
        }

        return response()->json(['success' => false, 'data' => 'not found'], 404);
    }
}
