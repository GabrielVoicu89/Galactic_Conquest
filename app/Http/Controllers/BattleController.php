<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Resource;
use App\Models\Ship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    public function attack($defenderId)
    {
        $attackerShips = Ship::where('user_id', Auth::user()->id)
            ->where('finished_at', '<', Carbon::now())
            ->where('claimed', true)
            ->get();
        $defenderShips = Ship::where('user_id', $defenderId)
            ->where('finished_at', '<', Carbon::now())
            ->where('claimed', true)
            ->get();

        // formula to calculate distance between 2 points
        function calculatePlanetDistance(int $x1, int $y1, int $x2, int $y2): float
        {
            $xDiff = abs($x1 - $x2);
            $yDiff = abs($y1 - $y2);
            // we divide by 10 since each ship consume a certain number of fuel for a distance of 10 unity
            return (int) round(sqrt(pow($xDiff, 2) + pow($yDiff, 2)) / 10);
        }

        // getting the planets for attacker and defender
        $attackerPlanet = Planet::where('user_id', Auth::user()->id)->first();
        // dd($attackerPlanet);
        $defenderPlanet = Planet::where('user_id', $defenderId)->first();

        //preparing the variables for the formula
        $x1 = $attackerPlanet->position_x;
        $y1 = $attackerPlanet->position_y;

        $x2 = $defenderPlanet->position_x;
        $y2 = $defenderPlanet->position_y;

        //calculating the distance with the formula
        $distance = calculatePlanetDistance($x1, $y1, $x2, $y2);
        // dd($attackerShips);
        if (count($attackerShips) > 0) {

            $fuelConsumed = 0;

            foreach ($attackerShips as $ship) {

                $fuelConsumption = $ship->fuel_consumption;

                $fuel = $distance * $fuelConsumption;

                $fuelConsumed += $fuel;
            }

            //getting the fuel for the attacker
            $resource = Resource::where('user_id', Auth::user()->id)->first();
            $attackerFuel = $resource->fuel;

            if ($fuelConsumed >= $attackerFuel) {
                // removing the fuel from the attacker's resource
                $resource->update(['fuel' => $attackerFuel - $fuelConsumed]);

                // ZA ATTACK

            } else {
                return response()->json(['message' => 'You do not have enough fuel for this destination.'], 401);
            }
        } else {
            return response()->json(['message' => 'You do not have any ship to attack with .'], 401);
        }









        return response()->json([
            // 'attackerShips' => $attackerShips,
            // 'defenderShips' => $defenderShips,
            // 'distance' => $distance,
            'fuelConsumed' => $fuelConsumed,
            'attackerFuel' => $attackerFuel
        ], 200);
    }
}
