<?php

use App\Http\Controllers\Admin\LocationsController as AdminLocationsController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/login', [LoginController::class, 'store']);
Route::get('/locations/{lat}/{lng}', [LocationsController::class, 'getLocation']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::resource('tasks', TaskController::class);
    Route::put('tasks/{task}/toggle', [TaskController::class, 'toggleComplete']);
    Route::resource('locations', LocationsController::class);

    Route::get('/user', function (Request $request) {
        $deviceId = $request->user()->device_id;

        if(!$deviceId && $request->device_id){
            $request->user()->update(['device_id' => $request->device_id]);
        }
        return $request->user();
    });

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'admin'], function () {
    Route::resource('tasks', AdminTaskController::class);
    Route::resource('locations', AdminLocationsController::class);
    Route::resource('users', UsersController::class);
});
