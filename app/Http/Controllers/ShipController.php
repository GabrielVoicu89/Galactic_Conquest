<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ship;
use App\Models\Resource;
use App\Models\ShipYard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShipController extends Controller
{
    public function buildHunter(Request $request, $shipYardId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorsFormatted = [];

            foreach ($errors as $field => $messages) {
                $errorsFormatted[$field] = $messages[0];
            }

            return response()->json(['errors' => $errorsFormatted], 400);
        }
        //getting the shipyard that will create the ship
        $shipYard = ShipYard::where('id', $shipYardId)->first();

        //checking if the shipyward is building a 
        if (!$shipYard->construction_state) {
            //getting the resources for the connected user
            $resources = Resource::where('user_id', Auth::user()->id)->first();

            $availableOre = $resources->ore;
            $availableEnergy = $resources->energy;

            //checking if the user has enough resources
            if ($availableOre >= 50) {
                $ship = new Ship();
                $ship->user_id = Auth::user()->id;
                $ship->ship_yard_id =  $shipYardId;
                $ship->type = $request->type;
                $ship->construction_cost = 50;
                $ship->energy_consumption = 1;
                $ship->finished_at = Carbon::now()->addHour();
                $ship->save();

                //setting the shipyard to state constructing
                ShipYard::where('id', $shipYardId)->update(['construction_state' => true]);

                $newAvailableOre = $availableOre - $ship->construction_cost;

                //updating the resources : ore and energy
                Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $availableEnergy - $ship->energy_consumption]);

                return response()->json(['message' => 'Hunter created successfully', 'ship' => $ship], 200);
            } else {
                return response()->json(['message' => 'You do not have enough resources'], 401);
            }
        } else {
            return response()->json(['message' => 'This ship yard already building a ship'], 201);
        }
    }
}
