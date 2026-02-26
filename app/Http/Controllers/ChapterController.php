<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChapterController extends Controller
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
        return view('admin.chapters.index');
    }

    /**
     * Return JSON list for DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $chapters = Chapter::orderBy('id')->get();

        $data = $chapters->map(function ($chapter) {
            return [
                'id' => $chapter->id,
                'name' => $chapter->name,
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
