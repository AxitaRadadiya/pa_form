<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FoodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.foods.index');
    }

    /**
     * Return JSON list for DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $foods = Food::orderBy('id')->get();

        $data = $foods->map(function ($food) {
            return [
                'id' => $food->id,
                'name' => $food->name,
                'action' => '',
            ];
        })->toArray();

        $count = count($data);

        return response()->json([
            'draw' => intval($request->get('draw', 1)),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $data,
        ]);
    }


    // Additional actions (create/store/edit/update/destroy) can be added as needed.
}
