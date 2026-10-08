<?php

namespace App\Providers;

use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'quotation' => Quotation::class,
            'purchase_order' => PurchaseOrder::class,
            'user' => User::class,
        ]);

        Paginator::useBootstrapFive();

        URL::forceScheme('https');
    }
}
