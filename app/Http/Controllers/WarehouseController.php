<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Resource;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;


class WarehouseController extends Controller
{
    public function defaultWarehouses($userId)
    {
        $userClaimedDefaultWarehouse = Warehouse::where('user_id', $userId)->first();

        if ($userClaimedDefaultWarehouse) {
            return response()->json(['message' => 'This user already claimed free warehouses.'], 401);
        } else {

            for ($i = 0; $i < 2; $i++) {
                $warehouse = new Warehouse();
                $warehouse->user_id = $userId;
                $warehouse->level = 1;
                $warehouse->capacity = 500;
                $warehouse->construction_cost = 500;
                $warehouse->finished_at = Carbon::now();
                $warehouse->save();
            }

            return response()->json(['warehouse' => $warehouse], 200);
        }
    }

    public function buildWarehouse()
    {

        $resources = Resource::where('user_id', Auth::user()->id)->first();
        $availableOre = $resources->ore;

        if ($availableOre >= 500) {

            $warehouse = new Warehouse();
            $warehouse->user_id = Auth::user()->id;
            $warehouse->level = 1;
            $warehouse->capacity = 500;
            $warehouse->construction_cost = 500;
            $warehouse->finished_at = Carbon::now()->addHours(1);
            $warehouse->save();

            $newAvailableOre = $availableOre - $warehouse->construction_cost;


            // update resource table decreasing the cost of the warehouse
            Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre]);

            return response()->json(['message' => 'Warehouse successfuly created', 'warehouse' => $warehouse], 200);
        } else {
            return response()->json(['message' => 'You do not have enough resources'], 401);
        }
    }

    public function getWarehouses()
    {
        $warehouses = Warehouse::where('user_id', Auth::user()->id)->get();
        // dd($warehouses);
        return response()->json(['warehouses' => $warehouses], 200);
    }
}
