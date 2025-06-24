<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::view('products', 'products.index')
    ->middleware(['auth', 'verified'])
    ->name('products.index');

// Livewire routes
Route::get('/customers', App\Livewire\Customer\Index::class)
    ->name('customers.index')
    ->middleware(['auth']);

Route::get('/customers/{customerId}', App\Livewire\Customer\Show::class)
    ->name('customers.show')
    ->middleware(['auth']);

require __DIR__.'/auth.php';
