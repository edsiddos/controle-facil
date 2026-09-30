<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Admin\AccountCardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\InstallmentPurchaseController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\RecurringTransactionController;

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

    Route::get('accounts/web-table', [AccountCardController::class, 'webTable'])->name('accounts.web-table');
    Route::resource('accounts', AccountCardController::class)->parameters(['accounts' => 'accountCard']);

    Route::get('categories/web-table', [CategoryController::class, 'webTable'])->name('categories.web-table');
    Route::resource('categories', CategoryController::class);

    Route::get('installment-purchases/web-table', [InstallmentPurchaseController::class, 'webTable'])->name('installment-purchases.web-table');
    Route::resource('installment-purchases', InstallmentPurchaseController::class)->parameters(['installment-purchases' => 'purchase']);

    Route::get('transactions/web-table', [TransactionController::class, 'webTable'])->name('transactions.web-table');
    Route::resource('transactions', TransactionController::class)->parameters(['transactions' => 'transaction']);

    Route::get('recurring-transactions/web-table', [RecurringTransactionController::class, 'webTable'])->name('recurring-transactions.web-table');
    Route::get('recurring-transactions', [RecurringTransactionController::class, 'index'])->name('recurring-transactions.index');
    Route::post('recurring-transactions/{recurringTransaction}/cancel', [RecurringTransactionController::class, 'cancel'])->name('recurring-transactions.cancel');
});

require __DIR__ . '/auth.php';
