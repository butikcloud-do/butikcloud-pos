<?php

namespace App\Providers;

use App\Lib\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
        Builder::macro("firstOrFailWithApi", function ($modelName = "data") {
            $data = $this->first();
            if (!$data) {
                throw new \Exception("custom_not_found_exception || The $modelName is not found", 404);
            }
            return $data;
        });

        Builder::macro("findOrFailWithApi", function ($modelName = "data", $id) {
            $data = $this->where("id", $id)->first();
            if (!$data) {
                throw new \Exception("custom_not_found_exception || The $modelName is not found", 404);
            }
            return $data;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
