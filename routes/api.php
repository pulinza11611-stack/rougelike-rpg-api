<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\ItemController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('customer')->group(function () {
    Route::post('/getByID', [UserController::class, 'getCustomerByID']);
});

Route::prefix('character')->group(function () {
    Route::post('/getByID', [CharacterController::class, 'getCharacterByUserID']);
    Route::post('/createByID', [CharacterController::class, 'createCharacter']);
});

Route::prefix('item')->group(function () {
    Route::post('/getByID', [ItemController::class, 'getItemsByUserID']);
});