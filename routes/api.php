<?php

use App\Http\Controllers\Api\TodoController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'todo', 'as' => 'todo.'], function () {
    Route::get('/', [TodoController::class, 'index']);
    Route::post('/', [TodoController::class, 'store']);
    Route::put('/{todo}', [TodoController::class, 'update']);
    Route::get('/{todo}', [TodoController::class, 'show']);
    Route::delete('/{todo}', [TodoController::class, 'destroy']);
    Route::patch('/{todo}/completed', [TodoController::class, 'completed']);
});
