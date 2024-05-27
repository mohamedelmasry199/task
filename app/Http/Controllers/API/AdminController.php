<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user'
        ]);

        $user = User::create([
            'user_name' => $request->user_name,
            'mobile_number' => $request->mobile_number,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
          return response()->json(['message' => 'User not found'], 404);
    }
        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
          return response()->json(['message' => 'User not found'], 404);
    }
        $request->validate([
            'user_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|unique:users,mobile_number,' . $user->id,
            'role' => 'required|in:admin,user'
        ]);

        $user->update([
            'user_name' => $request->user_name,
            'mobile_number' => $request->mobile_number,
            'role' => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json($user, 200);
    }

    public function destroy($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'User deleted successfully'], 200);
}

}
