<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $authUser = Auth::user(); // Get the logged-in user

    // If the user is an admin, show only users
    if ($authUser->role === 'admin') {
        $users = User::where([
            ['role', '=', 'user'],
            ['admin_id', '=', $authUser->id]
        ])->get()->load('beams', 'poles', 'highmasts')->makeHidden('admin_id');
    } 
    // If the user is a super_admin, show admins and users, but not super_admins
    elseif ($authUser->role === 'super_admin') {
        $users = User::whereIn('role', ['admin', 'user'])->get();
    } 
    // If the user is neither admin nor super_admin, deny access
    else {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return response()->json(['users' => $users], 200);
}

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        // Validate input fields
        $request->validate([
            'user_name' => 'required|string', 
            'password' => 'required|string',
        ]);
    
        // Find user by username
        $user = User::where('user_name', $request->user_name)->first();
    
        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        // Generate API token
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }


    public function logout(Request $request)
{
    // Revoke the user's current access token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logout successful'
    ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
{
    $authUser = Auth::user(); // Get the logged-in user

    // Only Super Admin can create Admin
    if ($request->role === 'admin' && $authUser->role !== 'super_admin') {
        return response()->json(['error' => 'Only Super Admin can create an Admin'], 403);
    }

    // If Super Admin creates a User, admin_id is required
    if ($request->role === 'user' && $authUser->role === 'super_admin') {
        $adminId = $request->admin_id;
    } 
    // If Admin creates a User, assign their ID automatically
    elseif ($request->role === 'user' && $authUser->role === 'admin') {
        $adminId = $authUser->id;
    } else {
        $adminId = null;
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'user_name' => $request->user_name,
        'password' => $request->password,
        'role' => $request->role,
        'admin_id' => $adminId,
    ]);

    return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
}


    /**
     * Display the specified resource.
     */
    public function show($user)
    {
        $user = User::find($user);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
