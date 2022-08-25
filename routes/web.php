<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'verified', 'role:super-admin|admin|moderator|developer'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Admins\AdminDashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('users', App\Http\Controllers\Admins\UserController::class)->except(['create', 'show', 'edit']);
    Route::resource('permissions', App\Http\Controllers\Admins\PermissionController::class)->except(['create', 'show', 'edit']);
    Route::resource('roles', App\Http\Controllers\Admins\RoleController::class)->except(['create', 'show', 'edit']);

    Route::resource('admins', App\Http\Controllers\Admins\AdminController::class)->parameters(['admins' => 'user'])->only(['index', 'update']);
    // Route::prefix('roles')->name('roles.')->group(function() {
    //     Route::get('/', [App\Http\Controllers\Admins\RoleController::class, 'index'])->name('index');
    //     Route::post('/', [App\Http\Controllers\Admins\RoleController::class, 'store'])->name('store');
    //     Route::patch('/{role}', [App\Http\Controllers\Admins\RoleController::class, 'update'])->name('update');
    //     Route::delete('/{role}', [App\Http\Controllers\Admins\RoleController::class, 'destroy'])->name('destroy');
    // });

    // Route::prefix('permissions')->name('permissions.')->group(function() {
    //     Route::get('/', [App\Http\Controllers\Admins\PermissionController::class, 'index'])->name('index');
    //     Route::post('/', [App\Http\Controllers\Admins\PermissionController::class, 'store'])->name('store');
    //     Route::patch('/{permission}', [App\Http\Controllers\Admins\PermissionController::class, 'update'])->name('update');
    //     Route::delete('/{permission}', [App\Http\Controllers\Admins\PermissionController::class, 'destroy'])->name('destroy');
    // });

    // Route::prefix('users')->name('users.')->group(function() {
    //     Route::get('/', [App\Http\Controllers\Admins\UserController::class, 'index'])->name('index');
    //     Route::post('/', [App\Http\Controllers\Admins\UserController::class, 'store'])->name('store');
    //     Route::patch('/{user}', [App\Http\Controllers\Admins\UserController::class, 'update'])->name('update');
    //     Route::delete('/{user}', [App\Http\Controllers\Admins\UserController::class, 'destroy'])->name('destroy');
    // });

    
    
});