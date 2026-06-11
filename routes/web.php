<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Admin\AccountCardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota para listar e exibir a interface em Vue.js
    Route::get('/accounts', [AccountCardController::class, 'index'])
        ->name('accounts.index');

    // Rota para submeter o formulário de criação (POST)
    Route::post('/accounts', [AccountCardController::class, 'store'])
        ->name('accounts.store');

    // Rota para eliminar um registo específico (DELETE)
    Route::delete('/accounts/{id}', [AccountCardController::class, 'destroy'])
        ->name('accounts.destroy');
});

require __DIR__.'/auth.php';
