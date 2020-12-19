<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\ClientController;

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

    // Auth system

        Route::post('/login', [LoginController::class, 'login'])->name('login');

        Route::any('/reject', [LoginController::class, 'reject'])->name('reject');

        Route::post('/reset-password', [PasswordController::class, 'send'])->name('reset.password.send');

        Route::put('/reset-password', [PasswordController::class, 'rest'])->name('reset.password.rest');

    // Clients

        Route::get('/clients/session/{dni}', [ClientController::class, 'sessionsList'])->name('client.session');

        Route::get('/clients/session/{dni}', [ClientController::class, 'sessionsList'])->name('client.sessions');

        Route::post('/clients/session', [ClientController::class, 'sessions'])->name('client.get.session');

        Route::get('/schedule', [ClientController::class, 'schedule'])->name('schedule');

    Route::middleware('auth:api')->group(function () {

        // Clients

            Route::name('clients.')->group(function () {

                Route::get('/clients', [ClientController::class, 'index'])->name('list');

                Route::post('/clients/imports', [ClientController::class, 'clientImports'])->name('import');

                Route::delete('/clients/session/cancel/{id}', [ClientController::class, 'sessionCancel'])->name('client.sessionCancel');
            });

        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
