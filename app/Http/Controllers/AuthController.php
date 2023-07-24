<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\SanctumServiceProvider;

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

            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 400);
        }

        $validated = $validator->validated();

        // Hashing the password for the database from the input "password" after the validation
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // +________________________________________________________+
        // |next redirecting in frontend to route auth.store_planet |
        // |________________________________________________________|



        return response()->json(['message' => 'User created successfully.', 'user' => $user], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 400);
        }


        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            $user = User::where('username', $request->username)->first();
            $token = $user->createToken('MyAppToken')->plainTextToken;

            // $request->session()->regenerate();
            // Auth::login(Auth::user());

            return response()->json([
                // to do token
                'message' => 'Loged successfully',
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'message' => 'Username or password invalid'
            ], 401);
        }
    }

    public function store_planet(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:planets',
        ]);



        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 400);
        }
        // user exist and send 404 ..... TO DO

        //security to check if the user already has a planet
        $userHasPlanet = Planet::where('user_id', $userId)->first();
        if ($userHasPlanet) {
            return response()->json(['message' => 'This user already has a planet.'], 401);
        } else {
            $planet = new Planet();
            $planet->name = $request->name;
            $planet->user_id = $userId;

            $uniquePosition = false;

            while (!$uniquePosition) {
                $position_y = rand(0, 999);
                $position_x = rand(0, 999);

                // Check if any planet already exists with the same position
                $existingPlanet = Planet::where('position_y', $position_y)
                    ->where('position_x', $position_x)
                    ->first();

                // If no existing planet with the same position is found, set the position for the current planet
                if (!$existingPlanet) {
                    $planet->position_y = $position_y;
                    $planet->position_x = $position_x;
                    $uniquePosition = true;
                }
            }

            $planet->save();


            // +________________________________________________________+
            // |                                                        |
            // |next redirecting in frontend to route default_resource  |
            // |________________________________________________________|

            return response()->json(['message' => 'Planet created successfully.', 'planet' => $planet], 200);
        }
    }

    public function getUser()
    {
        $user = User::where('id', Auth::user()->id)->get();

        return response()->json(['user' => $user], 200);
    }
}
