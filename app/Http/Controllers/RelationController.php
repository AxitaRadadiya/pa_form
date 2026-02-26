<?php

namespace App\Http\Controllers;

use App\Models\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RelationController extends Controller
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
        return view('admin.relations.index');
    }

    /**
     * Return JSON list for DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $relations = Relation::orderBy('id')->get();

        $data = $relations->map(function ($relation) {
            return [
                'id' => $relation->id,
                'name' => $relation->name,
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
}
