<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // 1. Validate the incoming JSON request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // 2. Create the User in the database
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // ALWAYS hash passwords
        ]);

        // 3. Generate the Sanctum API Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return the Token as a JSON response (React needs this!)
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201); // 201 Created
    }

    // Login an existing user
    public function login(Request $request)
    {
        // 1. Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Find the user by email
        $user = User::where('email', $request->email)->first();

        // 3. Check if user exists AND if the password matches the hash
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Throw an error that stops the request immediately
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // 4. Generate the API Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Return the Token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}