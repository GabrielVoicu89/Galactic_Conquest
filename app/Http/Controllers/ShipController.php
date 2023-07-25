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
        if ($request->type === "hunter") {
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

            //checking if the shipyard building is ready to use
            $finishedAt = Carbon::parse($shipYard->finished_at);
            $currentTime = Carbon::now();
            if ($finishedAt->lt($currentTime)) {

                //checking if the shipyward is building a ship 
                if (!$shipYard->construction_state) {
                    //getting the resources for the connected user
                    $resources = Resource::where('user_id', Auth::user()->id)->first();

                    $availableOre = $resources->ore;
                    $availableEnergy = $resources->energy;

                    //checking if the user has enough resources
                    if ($availableOre >= 50 && $availableEnergy >= 1) {
                        //creating the new ship
                        $ship = new Ship();
                        $ship->user_id = Auth::user()->id;
                        $ship->ship_yard_id =  $shipYardId;
                        $ship->type = $request->type;
                        $ship->construction_cost = 50;
                        $ship->attack = 50;
                        $ship->defense = 50;
                        $ship->energy_consumption = 1;
                        $ship->fuel_consumption = 1;
                        $ship->finished_at = Carbon::now()->addHour();
                        $ship->save();

                        //setting the shipyard to state constructing
                        ShipYard::where('id', $shipYardId)->update(['construction_state' => true]);

                        $newAvailableOre = $availableOre - $ship->construction_cost;

                        //updating the resources : ore and energy
                        Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $availableEnergy - $ship->energy_consumption]);

                        return response()->json(['message' => ucfirst($ship->type) . ' created successfully', 'ship' => $ship], 200);
                    } else {
                        return response()->json(['message' => 'You do not have enough resources'], 401);
                    }
                } else {
                    return response()->json(['message' => 'This ship yard already building a ship'], 201);
                }
            } else {
                return response()->json(['message' => 'Your ship yard is not ready to use'], 401);
            }
        } else {
            return response()->json(['message' => 'Route for hunter only'], 401);
        }
    }

    public function buildFrigate(Request $request, $shipYardId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);
        if ($request->type === "frigate") {
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

            //checking if the shipyard building is ready to use
            $finishedAt = Carbon::parse($shipYard->finished_at);
            $currentTime = Carbon::now();
            if ($finishedAt->lt($currentTime)) {

                //checking if the shipyward is building a ship 
                if (!$shipYard->construction_state) {
                    //getting the resources for the connected user
                    $resources = Resource::where('user_id', Auth::user()->id)->first();

                    $availableOre = $resources->ore;
                    $availableEnergy = $resources->energy;

                    //checking if the user has enough resources
                    if ($availableOre >= 200 && $availableEnergy >= 2) {
                        //creating the new ship
                        $ship = new Ship();
                        $ship->user_id = Auth::user()->id;
                        $ship->ship_yard_id =  $shipYardId;
                        $ship->type = $request->type;
                        $ship->construction_cost = 200;
                        $ship->attack = 200;
                        $ship->defense = 200;
                        $ship->energy_consumption = 2;
                        $ship->fuel_consumption = 2;
                        $ship->finished_at = Carbon::now()->addHours(2);
                        $ship->save();

                        //setting the shipyard to state constructing
                        ShipYard::where('id', $shipYardId)->update(['construction_state' => true]);

                        $newAvailableOre = $availableOre - $ship->construction_cost;

                        //updating the resources : ore and energy
                        Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $availableEnergy - $ship->energy_consumption]);

                        return response()->json(['message' => ucfirst($ship->type) . ' created successfully', 'ship' => $ship], 200);
                    } else {
                        return response()->json(['message' => 'You do not have enough resources'], 401);
                    }
                } else {
                    return response()->json(['message' => 'This ship yard already building a ship'], 201);
                }
            } else {
                return response()->json(['message' => 'Your ship yard is not ready to use'], 401);
            }
        } else {
            return response()->json(['message' => 'Route for frigate only'], 401);
        }
    }

    public function buildCruiser(Request $request, $shipYardId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);
        if ($request->type === "cruiser") {
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

            //checking if the shipyard building is ready to use
            $finishedAt = Carbon::parse($shipYard->finished_at);
            $currentTime = Carbon::now();
            if ($finishedAt->lt($currentTime)) {

                //checking if the shipyward is building a ship 
                if (!$shipYard->construction_state) {
                    //getting the resources for the connected user
                    $resources = Resource::where('user_id', Auth::user()->id)->first();

                    $availableOre = $resources->ore;
                    $availableEnergy = $resources->energy;

                    //checking if the user has enough resources
                    if ($availableOre >= 800 && $availableEnergy >= 4) {
                        //creating the new ship
                        $ship = new Ship();
                        $ship->user_id = Auth::user()->id;
                        $ship->ship_yard_id =  $shipYardId;
                        $ship->type = $request->type;
                        $ship->construction_cost = 800;
                        $ship->attack = 800;
                        $ship->defense = 800;
                        $ship->energy_consumption = 4;
                        $ship->fuel_consumption = 4;
                        $ship->finished_at = Carbon::now()->addHours(4);
                        $ship->save();

                        //setting the shipyard to state constructing
                        ShipYard::where('id', $shipYardId)->update(['construction_state' => true]);

                        $newAvailableOre = $availableOre - $ship->construction_cost;

                        //updating the resources : ore and energy
                        Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $availableEnergy - $ship->energy_consumption]);

                        return response()->json(['message' => ucfirst($ship->type) . ' created successfully', 'ship' => $ship], 200);
                    } else {
                        return response()->json(['message' => 'You do not have enough resources'], 401);
                    }
                } else {
                    return response()->json(['message' => 'This ship yard already building a ship'], 201);
                }
            } else {
                return response()->json(['message' => 'Your ship yard is not ready to use'], 401);
            }
        } else {
            return response()->json(['message' => 'Route for Cruiser only'], 401);
        }
    }

    public function buildDestroyer(Request $request, $shipYardId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);
        if ($request->type === "destroyer") {
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

            //checking if the shipyard building is ready to use
            $finishedAt = Carbon::parse($shipYard->finished_at);
            $currentTime = Carbon::now();
            if ($finishedAt->lt($currentTime)) {

                //checking if the shipyward is building a ship 
                if (!$shipYard->construction_state) {
                    //getting the resources for the connected user
                    $resources = Resource::where('user_id', Auth::user()->id)->first();

                    $availableOre = $resources->ore;
                    $availableEnergy = $resources->energy;

                    //checking if the user has enough resources
                    if ($availableOre >= 2000 && $availableEnergy >= 8) {
                        //creating the new ship
                        $ship = new Ship();
                        $ship->user_id = Auth::user()->id;
                        $ship->ship_yard_id =  $shipYardId;
                        $ship->type = $request->type;
                        $ship->construction_cost = 2000;
                        $ship->attack = 2000;
                        $ship->defense = 2000;
                        $ship->energy_consumption = 8;
                        $ship->fuel_consumption = 8;
                        $ship->finished_at = Carbon::now()->addHours(8);
                        $ship->save();

                        //setting the shipyard to state constructing
                        ShipYard::where('id', $shipYardId)->update(['construction_state' => true]);

                        $newAvailableOre = $availableOre - $ship->construction_cost;

                        //updating the resources : ore and energy
                        Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $availableEnergy - $ship->energy_consumption]);

                        return response()->json(['message' => ucfirst($ship->type) . ' created successfully', 'ship' => $ship], 200);
                    } else {
                        return response()->json(['message' => 'You do not have enough resources'], 401);
                    }
                } else {
                    return response()->json(['message' => 'This ship yard already building a ship'], 201);
                }
            } else {
                return response()->json(['message' => 'Your ship yard is not ready to use'], 401);
            }
        } else {
            return response()->json(['message' => 'Route for Destroyer only'], 401);
        }
    }

    public function claimShip($shipYardId)
    {
        //getting the last ship created by this shipyard since it can create only one at the time
        $ship = Ship::where('ship_yard_id', $shipYardId)
            ->where('user_id', Auth::user()->id)
            ->latest() // Orders the results by the 'created_at' column in descending order
            ->first(); // Retrieves the first (last in descending order) element of the result

        if (!$ship->claimed) {

            //checking if the ship is ready to claim
            $finishedAt = Carbon::parse($ship->finished_at);
            $currentTime = Carbon::now();
            // dd($currentTime->lt($finishedAt));

            if ($finishedAt->lt($currentTime)) {

                //when claiming the ship we set the shipyard to construction_state false
                ShipYard::where('id', $shipYardId)->update(['construction_state' => false]);

                //updating the resource since the shipyard consume only when building
                $resources = Resource::where('user_id', Auth::user()->id)->first();
                $availableEnergy = $resources->energy;
                Resource::where('user_id', Auth::user()->id)->update(['energy' =>  $availableEnergy + $ship->energy_consumption]);

                //setting the claimed status for this ship to true so it can be claimed only once
                $ship->update(['claimed' => true]);

                return response()->json(['message' => ucfirst($ship->type) . ' claimed successfully', 'ship' => $ship], 200);
            } else {

                return response()->json(['message' => "Your $ship->type is not ready to claim"], 401);
            }
        } else {
            return response()->json(['message' => "You  already claimed this $ship->type"], 401);
        }
    }

    public function getShipConstructing($shipYardId)
    {
        //getting the last ship created by this shipyard since it can create only one at the time
        $ship = Ship::where('ship_yard_id', $shipYardId)
            ->where('user_id', Auth::user()->id)
            ->latest() // Orders the results by the 'created_at' column in descending order
            ->first(); // Retrieves the first (last in descending order) element of the result

        return response()->json(['ship' => $ship]);
    }

    public function getAllShips()
    {
        $ships = Ship::where('user_id', Auth::user()->id)
            ->where('finished_at', '<', Carbon::now())
            ->where('claimed', true)
            ->get();
        $destroyers = $ships->where('type', 'destroyer');
        $frigates = $ships->where('type', 'frigate');
        $hunters = $ships->where('type', 'hunter');
        $cruisers = $ships->where('type', 'cruiser');

        return response()->json([
            'ships' => $ships,
            'destroyers' => $destroyers,
            'frigates' => $frigates,
            'cruisers' => $cruisers,
            'hunters' => $hunters,
        ], 200);
    }
}
