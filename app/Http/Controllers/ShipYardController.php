<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Resource;
use App\Models\ShipYard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipYardController extends Controller
{
    public function buildShipYard()
    {
        //recovering resources
        $resources = Resource::where('user_id', Auth::user()->id)->first();
        $availableOre = $resources->ore;

        //codition to check if has enough resources to create it
        if ($availableOre >= 1000) {
            $shipYard = new ShipYard();
            $shipYard->user_id = Auth::user()->id;
            $shipYard->construction_cost = 1000;
            $shipYard->finished_at = Carbon::now();
            $shipYard->save();

            $newAvailableOre = $availableOre - $shipYard->construction_cost;

            // update resource table decreasing the cost of the powerplant and increasing the energy that comes with the powerplant2
            Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre]);

            return response()->json(['message' => 'Ship yard successfuly created', 'shipYard' => $shipYard], 200);
        } else {

            return response()->json(['message' => 'You do not have enough resources'], 401);
        }
    }

    public function getShipYards()
    {
        $shipYards = ShipYard::where('user_id', Auth::user()->id)->get();

        return response()->json(['shipYards' => $shipYards], 200);
    }
}
