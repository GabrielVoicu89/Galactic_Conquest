<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateOreResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ore';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the ore resources each hour.';

    /**
     * Execute the console command.
     */

    public function handle()
    {

        $minesAndRefineries = DB::table('infrastructures')
            ->whereIn('type', ['mine', 'refinery'])
            ->get();

        foreach ($minesAndRefineries as $infrastructure) {
            $userId = $infrastructure->user_id;

            // Check if the infrastructure is finished (based on the finished_at column)
            $finishedAt = Carbon::parse($infrastructure->finished_at);
            $currentTime = Carbon::now();

            if ($currentTime->lt($finishedAt)) {
                // The infrastructure is not finished yet, skip production
                continue;
            }

            // Get the infrastructure's production_hour value
            $productionHour = $infrastructure->production_hour;

            // Get the infrastructure type (mine or refinery)
            $infrastructureType = $infrastructure->type;

            // Calculate the total warehouse capacity for the user
            $totalCapacity = DB::table('warehouses')
                ->where('user_id', $userId)
                ->sum('capacity');

            // Get the current resources for the user
            $resources = DB::table('resources')
                ->where('user_id', $userId)
                ->first();

            // Calculate the new resource value based on the production_hour
            if ($infrastructureType === 'mine') {
                $newResource = $resources->ore + $productionHour;
            } elseif ($infrastructureType === 'refinery') {
                $newResource = $resources->fuel + $productionHour;
            }

            // Limit the new resource value based on the warehouse capacity
            $newResource = min($newResource, $totalCapacity);

            // Update the resources table with the new resource value
            if ($infrastructureType === 'mine') {
                DB::table('resources')
                    ->where('user_id', $userId)
                    ->update(['ore' => $newResource]);
            } elseif ($infrastructureType === 'refinery') {
                DB::table('resources')
                    ->where('user_id', $userId)
                    ->update(['fuel' => $newResource]);
            }
        }

        $this->info('Ore and Fuel resources updated successfully.');
    }
}
