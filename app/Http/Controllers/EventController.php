<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
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
        return view('admin.events.index');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'qr_code' => 'nullable|image|max:2048',
            'used' => 'nullable|boolean',
        ]);

        $path = null;
        $qrPath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
        }
        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('events/qr', 'public');
        }

        $event = Event::create([
            'name' => $validated['name'],
            'image' => $path,
            'qr_code' => $qrPath,
            'description' => $validated['description'] ?? null,
            'used' => $request->has('used') ? 1 : 0,
        ]);

        return redirect()->route('events.index')->withSuccess('Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'qr_code' => 'nullable|image|max:2048',
            'used' => 'nullable|boolean',
        ]);

        $path = $event->image;
        $qrPath = $event->qr_code;
        if ($request->hasFile('image')) {
            // delete old if exists
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $path = $request->file('image')->store('events', 'public');
        }
        if ($request->hasFile('qr_code')) {
            if ($event->qr_code && Storage::disk('public')->exists($event->qr_code)) {
                Storage::disk('public')->delete($event->qr_code);
            }
            $qrPath = $request->file('qr_code')->store('events/qr', 'public');
        }

        $event->update([
            'name' => $validated['name'],
            'image' => $path,
            'qr_code' => $qrPath,
            'description' => $validated['description'] ?? null,
            'used' => $request->has('used') ? 1 : 0,
        ]);

        return redirect()->route('events.index')->withSuccess('Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        try {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
            $event->delete();
        } catch (\Exception $e) {
            Log::error('Event delete failed: '.$e->getMessage());
            return redirect()->route('events.index')->with('error', 'Failed to delete event.');
        }

        return redirect()->route('events.index')->withSuccess('Event deleted successfully.');
    }

    /**
     * Return JSON list for DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $events = Event::orderBy('id')->get();

        $data = $events->map(function ($event) {
            $viewUrl = route('events.show', $event->id);
            $editUrl = route('events.edit', $event->id);
            $deleteUrl = route('events.destroy', $event->id);

            $action = '';
            $action .= '<a href="' . $viewUrl . '" class="btn btn-sm btn-info mr-1" title="View"><i class="mdi mdi-eye"></i></a>';
            $action .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary mr-1" title="Edit"><i class="mdi mdi-pencil"></i></a>';
            $action .= '<form method="POST" action="' . $deleteUrl . '" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">'
                . '<input type="hidden" name="_token" value="' . csrf_token() . '">' 
                . '<input type="hidden" name="_method" value="DELETE">'
                . '<button class="btn btn-sm btn-danger" type="submit" title="Delete"><i class="mdi mdi-delete"></i></button>'
                . '</form>';

            return [
                'id' => $event->id,
                'image' => $event->image ? asset('storage/' . $event->image) : null,
                'qr_code' => $event->qr_code ? asset('storage/' . $event->qr_code) : null,
                'name' => $event->name,
                'description' => $event->description,
                'action' => $action,
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
    /**
     * Store a newly created event.
     */
    
}
