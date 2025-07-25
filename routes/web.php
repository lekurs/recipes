<?php

use App\Http\Controllers\LoginAsController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['App\Http\Middleware\HybridAuth'])
    ->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::view('products', 'products.index')
    ->middleware(['App\Http\Middleware\HybridAuth'])
    ->name('products.index');

// Livewire routes
Route::get('/customers', App\Livewire\Customer\Index::class)
    ->middleware(['auth'])
    ->name('customers.index');

Route::get('/customers/{customerId}', App\Livewire\Customer\Show::class)
    ->name('customers.show')
    ->middleware(['auth']);

Route::group(['middleware' => 'auth', 'prefix' => 'projects'], function () {
    Route::get('/{project}/{token?}', App\Livewire\Project\Show::class)
        ->name('projects.show');

    Route::get('/{project}/recipes/{recipe}', App\Livewire\Recipe\Show::class)
        ->name('recipes.show');
});


// Routes pour le système "Login As" (seulement en dev/staging)
Route::middleware(['auth', App\Http\Middleware\LoginAsMiddleware::class])->group(function () {
    Route::get('/admin/login-as', [LoginAsController::class, 'users'])->name('admin.login-as');
    Route::post('/admin/login-as/{user}', [LoginAsController::class, 'loginAs'])->name('admin.login-as.switch');
    Route::post('/admin/switch-back', [LoginAsController::class, 'switchBack'])->name('admin.switch-back');
});

require __DIR__.'/auth.php';
