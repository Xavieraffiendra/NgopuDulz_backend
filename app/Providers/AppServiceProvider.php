<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
// PANGGIL MODEL BARU KITA
use App\Models\MongoSanctumToken; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Paksa Sanctum pakai model MongoDB kita
        // Sanctum::usePersonalAccessTokenModel(MongoSanctumToken::class);
    }
}