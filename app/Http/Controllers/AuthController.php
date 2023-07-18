<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|max:100|min:4',
            'username' => 'required|string|unique:users',
            'birth_date' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d')
        ]);

        // checking for errors with the validator. if the validator has any errors we send it in a response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        // Hashing the password for the database from the input "password" after the validation
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json(['message' => 'User created successfully.', 'user' => $user], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }


        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            // $user = Auth::user();
            // $token = $user->createToken('MyAppToken')->accessToken;

            return response()->json([
                // to do token
                'message' => 'Loged successfully'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Username or password invalid'
            ], 401);
        }
    }
}
