<?php

namespace App\Http\Controllers;

use App\Models\PowerPlant;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PowerPlantController extends Controller
{
    public function buildPowerPlant()
    {
        $resources = Resource::where('user_id', Auth::user()->id)->first();
        $availableOre = $resources->ore;
        $energy = $resources->energy;

        //codition to check if has enough resources to create it
        if ($availableOre >= 500) {

            $powerPlant = new PowerPlant();
            $powerPlant->user_id = Auth::user()->id;
            $powerPlant->level = 1;
            $powerPlant->construction_cost = 500;
            $powerPlant->finished_at = Carbon::now()->addHours(1);
            $powerPlant->save();

            $newAvailableOre = $availableOre - $powerPlant->construction_cost;


            // update resource table decreasing the cost of the powerplant and increasing the energy that comes with the powerplant
            Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $energy + 5]);

            return response()->json(['message' => 'Power plant successfuly created', 'powerPlant' => $powerPlant], 200);
        } else {
            return response()->json(['message' => 'You do not have enough resources'], 401);
        }
    }
}
