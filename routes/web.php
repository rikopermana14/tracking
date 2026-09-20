<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RouteController;

Auth::routes();

Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/', function () {
        return view('auth.login');
    });

    // Logout
    Route::post('/logout', [
        App\Http\Controllers\Auth\LoginController::class,
        'logout'
    ])->name('logout');


    // =====================================================
    // ADMIN
    // =====================================================

    Route::middleware(['role:admin'])->group(function () {

        Route::get('/admin-page', [
            HomeController::class,
            'index'
        ])->name('admin.page');

        // User Management
        Route::get('/user', [
            HomeController::class,
            'create'
        ])->name('user');

        Route::post('/user', [
            HomeController::class,
            'store'
        ])->name('admin.users.store');

        Route::get('/users/{user}/edit', [
            HomeController::class,
            'edit'
        ])->name('admin.users.edit');

        Route::put('/users/{user}', [
            HomeController::class,
            'update'
        ])->name('admin.users.update');

        Route::delete('/users/{user}', [
            HomeController::class,
            'destroy'
        ])->name('admin.users.destroy');
         // Halaman editor route waypoint
    Route::get('/routes/create', [
        RouteController::class,
        'create'
    ])->name('routes.create');


    // Simpan route dan waypoint
    Route::post('/routes', [
        RouteController::class,
        'store'
    ])->name('routes.store');
    });


    // =====================================================
    // ADMIN + OPERATOR
    // =====================================================

    Route::middleware(['role:admin|operator'])->group(function () {

    Route::get(
    '/track-ship/historical-last-positions',
    [HomeController::class, 'historicalLastPositions']
);

        Route::get('/track-ship/filter', [
            HomeController::class,
            'filter'
        ])->name('track-ship.filter');

        Route::get('/track-ship/last-position', [
            HomeController::class,
            'getLastPosition'
        ])->name('track-ship.getLastPosition');

        Route::get('/track-ship/get-coordinates', [
            HomeController::class,
            'getCoordinatesByName'
        ]);

        Route::get('/history', [
            HomeController::class,
            'history'
        ]);

        // AIS
        Route::get('/track-ship/all-last-positions', [
            HomeController::class,
            'allLastPositions'
        ]);

        Route::get('/track-ship/search-destination', [
            HomeController::class,
            'searchDestination'
        ]);

        Route::get('/test-route', [
            HomeController::class,
            'testRoute'
        ]);

        Route::post('/track-ship/generate-route', [
            HomeController::class,
            'generateRoute'
        ]);
    });


    // =====================================================
    // OPERATOR
    // =====================================================

    Route::middleware(['role:operator'])->group(function () {

        Route::get('/operator-page', [
            HomeController::class,
            'index'
        ])->name('operator.page');

    });

});