<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dealers = Dealer::with([
            'projects' => function ($query) {
                $query->with(['beams', 'poles', 'highMasts']);
            }
        ])->get();
    
        return response()->json([
            'message' => 'Dealer details fetched successfully',
            'dealers' => $dealers
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    
    $validatedData = $request->validate([
        'dealer_id' => 'required|string|max:255|unique:dealers',
        'dealer_name' => 'required|string|max:255',
        'dealer_phone' => 'required|string|max:255|unique:dealers',
        'dealer_email' => 'required|string|email|max:255|unique:dealers',
        'user_name' => 'required|string|max:255|unique:dealers',
        'password' => 'required|string|min:8|confirmed', 
        'password_confirmation' => 'required',
        'location' => 'nullable|string|max:255'
    ]);

    
    $data = Arr::except($validatedData, ['password_confirmation']);

    Dealer::create($data);

    return response()->json([
        'message' => 'Dealer created successfully!'
    ], 201);
}

public function login(Request $request)
{
    // Validate input fields
    $validatedData = $request->validate([
        'user_name' => 'required|string|max:255',
        'password' => 'required|string'
    ]);

    // Find the dealer by username
    $dealer = Dealer::where('user_name', $validatedData['user_name'])->first();

    // If dealer not found, return error
    if (!$dealer) {
        return response()->json(['message' => 'Invalid username'], 401);
    }

    if (!$dealer->is_verify) {
        return response()->json(['message' => 'Your account is not verified. Contact admin'], 403);
    }
    

    // Check if the password is correct
    if (!Hash::check($validatedData['password'], $dealer->password)) {
        return response()->json(['message' => 'Invalid password'], 401);
    }

    // Generate an API token for authentication (if using Laravel Sanctum)
    $token = $dealer->createToken('dealer_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'id' => $dealer->id
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
     * Display the specified resource.
     */
    public function show($id)
    {
        $dealer = Dealer::with([
            'projects' => function ($query) {
                $query->with(['beams', 'poles', 'highMasts']);
            }
        ])->find($id);
    
        // Check if dealer exists
        if (!$dealer) {
            return response()->json(['message' => 'Dealer not found'], 404);
        }
    
        return response()->json([
            'message' => 'Dealer details fetched successfully',
            'dealer' => $dealer
        ], 200);
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
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        
    }
}
