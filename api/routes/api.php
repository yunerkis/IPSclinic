<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\CategoryController;

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

    // Informations

        Route::get('/clients/session/{dni}', [ClientController::class, 'sessionsActive'])->name('client.session.active');

        Route::post('/clients/session', [ClientController::class, 'sessions'])->name('client.get.session');

        Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.list');

    Route::middleware('auth:api')->group(function () {

        // Clients

            Route::name('clients.')->group(function () {

                Route::get('/clients', [ClientController::class, 'index'])->name('list');

                Route::post('/clients/imports', [ClientController::class, 'clientImports'])->name('import');

                Route::get('/clients/sessions', [ClientController::class, 'sessionsList'])->name('client.sessions');

                Route::delete('/clients/session/cancel/{id}', [ClientController::class, 'sessionCancel'])->name('client.sessionCancel');
            });

            Route::name('categories.')->group(function () {

                Route::get('/categories', [CategoryController::class, 'index'])->name('list');

                Route::post('/categories', [CategoryController::class, 'store'])->name('store');

                Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('update');

                Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('destroy');
            });

            Route::name('doctors.')->group(function () {

                Route::post('/doctors', [DoctorController::class, 'store'])->name('store');

                Route::get('/doctors/{id}', [DoctorController::class, 'show'])->name('list');

                Route::put('/doctors/{id}', [DoctorController::class, 'update'])->name('update');

                Route::delete('/doctors/{id}', [DoctorController::class, 'destroy'])->name('destroy');
            });

        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
