<?php

use App\Http\Controllers\Api\DomainController;
use App\Http\Controllers\Api\UserController;
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

Route::prefix('v1')->group(function () {
    Route::get('/domain', [DomainController::class, 'index'])->name('domain.index');

    Route::get('/user/{user}/domains', [UserController::class, 'domains'])->name('user.domains');
});
