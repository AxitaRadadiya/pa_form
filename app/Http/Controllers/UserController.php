<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.users.index');
    }

    public function list(Request $request): JsonResponse
    {
        $users = User::where('role', 'user')->orderBy('id')->get();

        $data = $users->map(function ($user) {
            $show = route('users.show', $user->id);
            $edit = route('users.edit', $user->id);
            $del = route('users.destroy', $user->id);

            $action = '<a href="'.$show.'" class="btn btn-sm btn-info mr-1"><i class="mdi mdi-eye"></i></a>';
            $action .= '<a href="'.$edit.'" class="btn btn-sm btn-primary mr-1"><i class="mdi mdi-pencil"></i></a>';
            $action .= '<form method="POST" action="'.$del.'" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">'
                . '<input type="hidden" name="_token" value="'.csrf_token().'">'
                . '<input type="hidden" name="_method" value="DELETE">'
                . '<button class="btn btn-sm btn-danger" type="submit"><i class="mdi mdi-delete"></i></button>'
                . '</form>';

            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'mobile' => $user->mobile,
                'email' => $user->email,
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

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:30',
            'email' => 'required|email|unique:users,email',
        ]);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'mobile' => $validated['mobile'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(12)),
            'name' => $validated['first_name'].' '.$validated['last_name'],
        ]);

        return redirect()->route('users.index')->withSuccess('User created successfully.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:30',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->mobile = $validated['mobile'] ?? null;
        $user->email = $validated['email'];
        $user->name = $validated['first_name'].' '.$validated['last_name'];
        $user->save();

        return redirect()->route('users.index')->withSuccess('User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->withSuccess('User deleted successfully.');
    }
}
