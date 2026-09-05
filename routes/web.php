<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use Spatie\Permission\Middlewares\RoleMiddleware;
use App\Http\Controllers\HomeController;


Auth::routes();
// Grup middleware berdasarkan role

Route::middleware(['web','auth'])->group(function () {

    Route::get('/', function () {
        return view('auth.login');
    });

    // Rute logout
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    // Rute admin
    Route::middleware(['role:admin|purchasing'])->group(function () {
        Route::get('/admin-page', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.page');
        Route::get('/user', [App\Http\Controllers\HomeController::class, 'create'])->name('user');
        Route::post('/user', [App\Http\Controllers\HomeController::class, 'store'])->name('admin.users.store');
        Route::get('users/{user}/edit', [App\Http\Controllers\HomeController::class, 'edit'])->name('admin.users.edit');
        Route::put('users/{user}', [App\Http\Controllers\HomeController::class, 'update'])->name('admin.users.update');
        Route::delete('users/{user}', [App\Http\Controllers\HomeController::class, 'destroy'])->name('admin.users.destroy');
       
        Route::get('/track-ship/filter', [App\Http\Controllers\HomeController::class, 'filter'])->name('track-ship.filter');
        Route::get('/track-ship/last-position', [App\Http\Controllers\HomeController::class, 'getLastPosition'])->name('track-ship.getLastPosition');
        Route::get('/track-ship/get-coordinates', [App\Http\Controllers\HomeController::class, 'getCoordinatesByName']);
        Route::get('/history', [App\Http\Controllers\HomeController::class, 'history']);
        Route::get('/track-ship/all-last-positions', [App\Http\Controllers\HomeController::class, 'allLastPositions']);
        Route::get('/track-ship/search-destination', [App\Http\Controllers\HomeController::class,  'searchDestination']);
        // Tambahkan rute admin lainnya di sini
        Route::get('/test-route',[App\Http\Controllers\HomeController::class,'testRoute']);
        Route::post('/route-planner-test',[App\Http\Controllers\RouteController::class, 'generateRoute']);
    });

Route::post(
    '/track-ship/generate-route',
    [App\Http\Controllers\HomeController::class,'generateRoute']
);

    // Rute operator
    Route::middleware(['role:purchasing'])->group(function () {
        Route::get('/purchasing-page', [App\Http\Controllers\HomeController::class, 'index'])->name('purchasing.page');
        // Tambahkan rute operator lainnya di sini
            Route::get('/track-ship/filter', [App\Http\Controllers\HomeController::class, 'filter'])->name('track-ship.filter');
        Route::get('/track-ship/last-position', [App\Http\Controllers\HomeController::class, 'getLastPosition'])->name('track-ship.getLastPosition');
        Route::get('/track-ship/get-coordinates', [App\Http\Controllers\HomeController::class, 'getCoordinatesByName']);
        Route::get('/history', [App\Http\Controllers\HomeController::class, 'history']);
        Route::get('/track-ship/all-last-positions', [App\Http\Controllers\HomeController::class, 'allLastPositions']);
        Route::get('/track-ship/search-destination', [App\Http\Controllers\HomeController::class,  'searchDestination']);
        // Tambahkan rute admin lainnya di sini
        // Tambahkan rute admin lainnya di sini
        Route::get('/test-route',[App\Http\Controllers\HomeController::class,'testRoute']);
        Route::post('/route-planner-test',[App\Http\Controllers\RouteController::class, 'generateRoute']);
    });

  
});
