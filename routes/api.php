<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\ShipController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ShipYardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PowerPlantController;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\PlanetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['middleware' => ['web']], function () {

// });

//public routes

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register/planet/{userId}', [AuthController::class, 'store_planet'])->name('auth.store_planet'); // acces it only once after register
Route::post('/resource/{userId}', [ResourceController::class, 'defaultResource'])->name('default_resource'); // acces it only once after creating the planet
Route::post('/default_warehouses/{userId}', [WarehouseController::class, 'defaultWarehouses'])->name('default_warehouses'); // acces it only once after claiming the default resources

//route mail:
Route::post('/sendemailreset', [AuthController::class, 'sendEmailPasswordReset']);
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');


Route::middleware('auth:sanctum')->group(function () {

    //protected routes

    //USERS
    Route::get('/user', [AuthController::class, 'getUser']);

    //RESOURCE
    Route::get('/resource', [ResourceController::class, 'getResource']);

    //INFRASTRUCTURE
    Route::put('/infrastructures/{infrastructureId}/upgrade', [InfrastructureController::class, 'upgradeInfrastructure']);
    Route::post('/infrastructures/{infrastructureId}/destroy', [InfrastructureController::class, 'destroyInfrastructure']);

    // MINE
    Route::post('/create/mine', [InfrastructureController::class, 'buildMine'])->name('store_mine');
    Route::get('/mines', [InfrastructureController::class, 'getMines'])->name('get_mines');


    //REFINERY
    Route::post('/create/refinery', [InfrastructureController::class, 'buildRefinery'])->name('store_refinery');
    Route::get('/refineries', [InfrastructureController::class, 'getRefineries'])->name('get_refineries');

    //POWER PLANTS
    Route::post('/create/powerplant', [PowerPlantController::class, 'buildPowerPlant'])->name('store_power_plant');
    Route::get('/powerplants', [PowerPlantController::class, 'getPowerPlants'])->name('get_powerplants');

    //WAREHOUSES
    Route::post('/create/warehouse', [WarehouseController::class, 'buildWarehouse'])->name('store_warehouse');
    Route::get('/warehouses', [WarehouseController::class, 'getWarehouses'])->name('get_warehouse');

    //SHIP YARDS
    Route::post('/create/shipyard', [ShipYardController::class, 'buildShipYard']);
    Route::get('/shipyards', [ShipYardController::class, 'getShipYards']);

    //SHIPS
    Route::post('/create/hunter/{shipYardId}', [ShipController::class, 'buildHunter']);
    Route::post('/create/frigate/{shipYardId}', [ShipController::class, 'buildFrigate']);
    Route::post('/create/cruiser/{shipYardId}', [ShipController::class, 'buildCruiser']);
    Route::post('/create/destroyer/{shipYardId}', [ShipController::class, 'buildDestroyer']);
    Route::get('/ships', [ShipController::class, 'getAllShips']);
    Route::get('/shipyard/{shipYardId}/constructing_ship', [ShipController::class, 'getShipConstructing']);
    Route::post('/claim/ship/{shipYardId}', [ShipController::class, 'claimShip']);

    //PLANETS
    Route::get('/positions', [PlanetController::class, 'getPositions']);
    Route::get('/planet', [PlanetController::class, 'getPlanet']);

    //BATTLES
    Route::post('/attack/{defenderId}', [BattleController::class, 'attack']);
    Route::get('/rankings', [BattleController::class, 'getRanking']);

    //LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);
});
