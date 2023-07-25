<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ship;
use App\Models\Planet;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    private function calculatePoints($ships, $type)
    {
        // Calculate total points for the provided ships
        $totalPoints = 0;
        foreach ($ships as $ship) {
            $totalPoints += $ship->$type * rand(5, 15) / 10;;  // random factor between 0.5 and 1.5
        }

        return $totalPoints;
    }

    private function removeShips(&$ships)
    {
        // Remove 30% of the ships, prioritizing the weakest ones
        // Sort by defense_points to ensure weakest are removed first
        $ships = $ships->sortBy('defense');

        $toDestroy = ceil($ships->count() * 0.30);  // 30% of this ship type, rounded up

        for ($i = 0; $i < $toDestroy; $i++) {
            // Remove the ship and delete it from the database
            $ship = $ships->shift();
            $ship->delete();
        }
    }

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


        if (count($attackerShips) > 0) {

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

            $fuelConsumed = 0;

            foreach ($attackerShips as $ship) {

                $fuelConsumption = $ship->fuel_consumption;

                $fuel = $distance * $fuelConsumption;

                $fuelConsumed += $fuel;
            }

            //getting the fuel for the attacker
            $resource = Resource::where('user_id', Auth::user()->id)->first();
            $attackerFuel = $resource->fuel;

            if ($fuelConsumed <= $attackerFuel) {
                // removing the fuel from the attacker's resource
                $resource->update(['fuel' => $attackerFuel - $fuelConsumed]);

                // ZA ATTACK
                $round = 1;
                $battleLog = [];

                while (count($attackerShips) > 0 && count($defenderShips) > 0) {
                    // Calculate the total attack points and defense points for both attacker and defender
                    $attackerPoints = $this->calculatePoints($attackerShips, 'attack');
                    $defenderPoints = $this->calculatePoints($defenderShips, 'defense');

                    // Determine the round winner and apply losses
                    if ($attackerPoints > $defenderPoints) {
                        // Attacker wins the round
                        $this->removeShips($defenderShips);
                        $roundWinner = 'Attacker';
                    } else {
                        // Defender wins the round
                        $this->removeShips($attackerShips);
                        $roundWinner = 'Defender';
                    }

                    // Add battle details to the battle log
                    $battleLog[] = [
                        'round' => $round,
                        'winner' => $roundWinner,
                        'attacker_ships' => count($attackerShips),
                        'defender_ships' => count($defenderShips),
                    ];

                    // Increment the round number
                    $round++;
                }



                return response()->json([
                    // 'attackerShips' => $attackerShips,
                    // 'defenderShips' => $defenderShips,
                    // 'distance' => $distance,
                    'fuelConsumed' => $fuelConsumed,
                    'attackerFuel' => $attackerFuel,
                    'battle_log' => $battleLog
                ], 200);
            } else {
                return response()->json(['message' => 'You do not have enough fuel for this destination.', 'fuelConsumed' => $fuelConsumed, 'attackerFuel' => $attackerFuel,], 401);
            }
        } else {
            return response()->json(['message' => 'You do not have any ship to attack with .'], 401);
        }
    }
}
