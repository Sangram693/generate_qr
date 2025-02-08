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
    $authUser = Auth::user(); 

    
    if ($authUser->role === 'admin') {
        $users = User::where([
            ['role', '=', 'user'],
            ['admin_id', '=', $authUser->id]
        ])->get()->load('beams', 'poles', 'highmasts')->makeHidden('admin_id');
    } 
    
    elseif ($authUser->role === 'super_admin') {
        $users = User::whereIn('role', ['admin', 'user'])->get();
    } 
    
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
        
        $request->validate([
            'user_name' => 'required|string', 
            'password' => 'required|string',
        ]);
    
        
        $user = User::where('user_name', $request->user_name)->first();
    
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'id' => $user->id,
        ], 200);
    }


    public function logout(Request $request)
{
    
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
    $authUser = Auth::user(); 

    
    if ($request->role === 'admin' && $authUser->role !== 'super_admin') {
        return response()->json(['error' => 'Only Super Admin can create an Admin'], 403);
    }

    
    if ($request->role === 'user' && $authUser->role === 'super_admin') {
        $adminId = $request->admin_id;
    } 
    
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
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
{
    $authUser = Auth::user(); 
    $user = User::find($id);
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    if ($authUser->role === 'user' && (int)$id !== (int)$authUser->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    
    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone
    ]);

    return response()->json(['message' => 'User updated successfully'], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        
    }
}
