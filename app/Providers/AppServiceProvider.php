<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::enablePasswordGrant();
        Passport::tokensExpireIn(now()->addHour());
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::tokensCan([
            'profile:read' => 'Consultar el perfil del usuario',
            'products:read' => 'Consultar productos',
            'products:create' => 'Crear productos',
            'products:update' => 'Actualizar productos',
            'products:delete' => 'Eliminar productos',
        ]);
    }
}
