<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;




Route::get('/', function () {
    return response()->json([
        'api_name' => 'Laravel API + JWT.',
        'api_version' => '1.0.1'
    ]);
});


Route::group([

    'middleware' => 'api',
    'prefix' => 'api'

], function ($router) {


    Route::delete('/user/{id}', [UserController::class, 'delete']);
    Route::patch('/user/{id}', [UserController::class, 'update']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::get('/list-users', [UserController::class, 'index']);
    Route::post('/register', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login']);

    
});
