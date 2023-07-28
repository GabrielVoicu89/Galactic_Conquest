<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use Carbon\Carbon;
use App\Models\Ship;
use App\Models\Planet;
use App\Models\Resource;
use App\Models\User;
use App\Models\Warehouse;
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

        $destroyedShips = [];  // Store the types of destroyed ships

        for ($i = 0; $i < $toDestroy; $i++) {

            // Remove the ship  from the collection.
            $ship = $ships->shift();

            //if this type of ship wasnt destroyed yet we set it to 0 first and after we add one with $destroyedShips[$ship->type]++;
            if (!isset($destroyedShips[$ship->type])) {
                $destroyedShips[$ship->type] = 0;
            }
            $destroyedShips[$ship->type]++;

            //Remove the ship  from the database
            $ship->delete();
        }

        return $destroyedShips;
    }

    public function attack($defenderId)
    {

        // TO DO condition for attacking urself

        //getting the ships for attacker and defender 
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

            //calculating the fuel consumption for attacker ships
            foreach ($attackerShips as $ship) {

                $fuelConsumption = $ship->fuel_consumption;

                $fuel = $distance * $fuelConsumption;

                $fuelConsumed += $fuel;
            }

            //getting the resource for attacker and defender
            $resourceAttacker = Resource::where('user_id', Auth::user()->id)->first();
            $resourceDefender = Resource::where('user_id', $defenderId)->first();

            //getting the fuel for the attacker
            $attackerFuel = $resourceAttacker->fuel;

            if ($fuelConsumed <= $attackerFuel) {
                // removing the fuel from the attacker's resource
                $resourceAttacker->update(['fuel' => $attackerFuel - $fuelConsumed]);

                // ZA ATTACK
                $round = 1;
                $battleLog = [];
                $attacker = User::where('id', Auth::user()->id)->first()->username;
                $defender = User::where('id', $defenderId)->first()->username;

                while (count($attackerShips) > 0 && count($defenderShips) > 0) {
                    // Calculate the total attack points and defense points for both attacker and defender
                    $attackerPoints = $this->calculatePoints($attackerShips, 'attack');
                    $defenderPoints = $this->calculatePoints($defenderShips, 'defense');

                    // Determine the round winner and apply losses
                    if ($attackerPoints > $defenderPoints) {
                        // Attacker wins the round
                        $destroyedShips = $this->removeShips($defenderShips);
                        $roundWinner = $attacker;
                    } else {
                        // Defender wins the round
                        $destroyedShips = $this->removeShips($attackerShips);
                        $roundWinner = $defender;
                    }

                    // Add battle details to the battle log
                    $battleLog[] = [
                        'round' => $round,
                        'winner' => $roundWinner,
                        'attacker_ships' => count($attackerShips),
                        'defender_ships' => count($defenderShips),
                        'destroyed_ships' => $destroyedShips,  // Include destroyed ship types in the battle log
                    ];

                    // Increment the round number
                    $round++;
                }

                // deciding the winner 
                $battleWinner = '';

                if (count($attackerShips) > 0) {
                    $battleWinner = $attacker;
                } else {
                    $battleWinner = $defender;
                }

                // adding the winner to battle log
                // $battleLog['overall_winner'] = $battleWinner;

                if ($battleWinner === $attacker) {
                    //calculate 10% of the resources from the defender to add it to the attacker resource
                    $gainedOre = ceil($resourceDefender->ore * 0.1);
                    $gainedFuel = ceil($resourceDefender->fuel * 0.1);

                    // adding the gained or to the attacker
                    $totalAttackerOre = $resourceAttacker->ore + $gainedOre;
                    $totalAttackerFuel = $resourceAttacker->fuel + $gainedFuel;

                    //getting the capacity of warehouses
                    $totalCapacity = Warehouse::where('user_id', Auth::user()->id)
                        ->where('finished_at', '<', Carbon::now())
                        ->sum('capacity');

                    //updating the database for the attacker with the limity of warehouses
                    Resource::where('user_id', Auth::user()->id)->update(['ore' => min($totalAttackerOre, $totalCapacity)]);
                    Resource::where('user_id', Auth::user()->id)->update(['fuel' => min($totalAttackerFuel, $totalCapacity)]);

                    //removing the gained resources from the defender
                    $totalDefenderOre = $resourceDefender->ore - $gainedOre;
                    $totalDefenderFuel = $resourceDefender->fuel - $gainedFuel;

                    //updating the database for the defender
                    Resource::where('user_id', $defenderId)->update(['ore' => $totalDefenderOre]);
                    Resource::where('user_id', $defenderId)->update(['fuel' => $totalDefenderFuel]);

                    // creating the battle column in the database if the attacker win
                    $battle = new Battle();
                    $battle->attacker_id = Auth::user()->id;
                    $battle->defender_id = $defenderId;
                    $battle->win = true;
                    $battle->save();

                    $winner =  User::where('id', Auth::user()->id)->first();
                    $winner->update(['victories' => $winner->victories + 1]);

                    return response()->json([
                        // 'attackerShips' => $attackerShips,
                        // 'defenderShips' => $defenderShips,
                        // 'distance' => $distance,
                        // 'fuelConsumed' => $fuelConsumed,
                        // 'attackerFuel' => $attackerFuel,
                        'battle_log' => $battleLog,
                        'overall_winner' => $battleWinner,
                        'gainedOre' => $gainedOre,
                        'gainedFuel' => $gainedFuel
                    ], 201);
                } else {

                    // creating the battle column in the database if the attacker loose
                    $battle = new Battle();
                    $battle->attacker_id = Auth::user()->id;
                    $battle->defender_id = $defenderId;
                    $battle->win = false;
                    $battle->save();

                    $winner =  User::where('id', $defenderId)->first();
                    $winner->update(['victories' => $winner->victories + 1]);

                    return response()->json([
                        // 'attackerShips' => $attackerShips,
                        // 'defenderShips' => $defenderShips,
                        // 'distance' => $distance,
                        // 'fuelConsumed' => $fuelConsumed,
                        // 'attackerFuel' => $attackerFuel,
                        'battle_log' => $battleLog,
                        'overall_winner' => $battleWinner,

                    ], 201);
                }
            } else {
                return response()->json(['message' => 'You do not have enough fuel for this destination.', 'fuelConsumed' => $fuelConsumed, 'distance' => $distance * 10], 401);
            }
        } else {
            return response()->json(['message' => 'You do not have any ship to attack with .'], 401);
        }
    }

    public function getRanking()
    {
        $ranking = User::orderBy('victories', 'desc')->get();

        return response()->json($ranking, 200);
    }
}
