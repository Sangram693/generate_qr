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
        ])->get()->makeHidden('admin_id');
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
    
        
        $abilities = [];
    if ($user->role === 'admin') {
        $abilities[] = 'origin:' . $user->origin;
    }
    
    // Create the token with abilities
    $token = $user->createToken('auth_token', $abilities)->plainTextToken;
    
        return response()->json([
            'isLogin' => true,
            'token' => $token,
            'email' => $user->email,
            'userName' => $user->user_name,
            'role' => $user->role,
            'origin' => $user->origin,
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

    // return $request->all();

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'user_name' => $request->user_name,
        'password' => $request->password,
        'role' => $request->role,
        'admin_id' => $adminId,
        'origin' => $request->origin
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
    
    // Authorization check:
    if ($authUser->role === 'user' && (int)$authUser->id !== (int)$user->id) {
        // Regular user can update only themselves.
        return response()->json(['error' => 'Unauthorized'], 403);
    } elseif ($authUser->role === 'admin') {
        // Admin can update only if the target user's admin_id matches the admin's id.
        if ($user->admin_id != $authUser->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
    // Super admin can update any user; no extra check is needed.

    // Prepare data for update.
    $data = [
        'name'      => $request->name ?? $user->name,
        'email'     => $request->email ?? $user->email,
        'phone'     => $request->phone ?? $user->phone,
        'user_name' => $request->user_name ?? $user->user_name,
    ];

    // If a password is provided, hash and update it.
    if ($request->filled('password')) {
        $data['password'] = $request->password;
    }

    $user->update($data);

    return response()->json(['message' => 'User updated successfully'], 200);
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        
    }

    public function changePassword(Request $request)
{
    $authUser = Auth::user();
    $user = User::find($authUser->id);
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    

    // Validate input
    $request->validate([
        'old_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    // Check if old password is correct
    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json(['error' => 'Old password is incorrect'], 400);
    }

    // Update password
    $user->update([
        'password' => $request->new_password
    ]);

    return response()->json(['message' => 'Password updated successfully'], 200);
}

}
