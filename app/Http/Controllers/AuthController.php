<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Planet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

            return response()->json([
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





    public function sendEmailPasswordReset(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 401);
        }

        // getting the user by email with the static function getEmail that is declared in User model
        $user = User::getEmail($request->email);

        // checking if the email belongs to an user
        if (!empty($user)) {
            //generating a token in the remember_token column
            $user->remember_token = Str::random(64);
            $user->save();

            //sending the mail 
            Mail::to($user->email)->send(new ResetPasswordMail($user));

            return response()->json(['message' => 'Please check your email to reset your password'], 201);
        } else {
            //if the email does not exist in the database
            return response()->json(['error' => 'Email not found in the system.'], 401);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:4',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 401);
        }

        // checking the token with the getToken 
        $user = User::getToken($request->token);

        if (!empty($user)) {

            //creating the new password and change the remember_token so it can't be used again
            $newPassword = $request->password;
            $user->password = Hash::make($newPassword);
            $user->remember_token = Str::random(64);
            $user->save();

            return response()->json(['message' => 'Password changed successfuly'], 201);
        } else {
            return response()->json(['message' => 'Wrong token'], 401);
        }
    }

    public function logout()
    {
        DB::table('personal_access_tokens')->where('tokenable_id', Auth::user()->id)->delete();
        return response()->json(['message' => 'Logout successfuly'], 201);
    }
}
