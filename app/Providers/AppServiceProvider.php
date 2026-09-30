<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AccountCardRepositoryInterface;
use App\Repositories\Eloquent\AccountCardRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Contracts\InstallmentPurchaseRepositoryInterface;
use App\Repositories\Eloquent\InstallmentPurchaseRepository;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Eloquent\TransactionRepository;
use App\Repositories\Contracts\RecurringTransactionRepositoryInterface;
use App\Repositories\Eloquent\RecurringTransactionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccountCardRepositoryInterface::class, AccountCardRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(InstallmentPurchaseRepositoryInterface::class, InstallmentPurchaseRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(RecurringTransactionRepositoryInterface::class, RecurringTransactionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
