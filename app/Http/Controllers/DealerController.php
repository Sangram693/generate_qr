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

    
    $dealer = Dealer::create($validatedData);

    return response()->json([
        'message' => 'Dealer created successfully!',
        'dealer' => $dealer
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

    // Check if the password is correct
    if (!Hash::check($validatedData['password'], $dealer->password)) {
        return response()->json(['message' => 'Invalid password'], 401);
    }

    // Generate an API token for authentication (if using Laravel Sanctum)
    $token = $dealer->createToken('dealer_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token, // Send token for API authentication
        'dealer' => $dealer
    ], 200);
}

    /**
     * Display the specified resource.
     */
    public function show(Dealer $dealer)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dealer $dealer)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dealer $dealer)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dealer $dealer)
    {
        
    }
}
