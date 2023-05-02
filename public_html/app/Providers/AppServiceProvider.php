<?php

namespace App\Providers;

use App\Exceptions\Handler;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use MetaFox\Platform\ApiResourceManager;
use MetaFox\Platform\ModuleManager;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Routing\ResourceRegistrar;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;
use Symfony\Component\Debug\ExceptionHandler;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        BaseResourceRegistrar::class => ResourceRegistrar::class,
        ExceptionHandler::class      => Handler::class,
    ];

    public array $singletons = [
        ExceptionHandler::class => Handler::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // https://laravel.com/docs/9.x/routing#parameters-global-constraints
        Route::pattern('id', '[0-9]+');

        // fix issue for laravel route() ...
        URL::forceScheme(config('app.force_protocol'));
        URL::forceRootUrl(config('app.url'));

        if ($this->app->runningInConsole()
            && !$this->app->runningUnitTests()) {
            /** @link https://laravel.com/docs/8.x/packages#migrations */
            $paths = ModuleManager::instance()->getDatabaseMigrationsFrom();
            if (!empty($paths)) {
                $this->loadMigrationsFrom($paths);
            }
        }

        Builder::macro('addScope', function (BaseScope $scope): Builder {
            /** @var Builder $query */
            $query = $this;

            $scope->apply($query, $query->getModel());

            return $query;
        });

        QueryBuilder::macro('addScope', function (BaseScope $scope): QueryBuilder {
            /** @var QueryBuilder $query */
            $query = $this;
            $scope->applyQueryBuilder($query);

            return $query;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
        // Use passport routes.
        Passport::routes();
        // Set expire time.
        Passport::tokensExpireIn(now()->addDays(config('auth.passport_token_expire_time')));
        Passport::refreshTokensExpireIn(now()->addDays(config('auth.passport_refresh_token_expire_time')));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        /* @see \MetaFox\Platform\Facades\Module */
        $this->app->singleton('Modules', ModuleManager::class);

        /* @see \MetaFox\Platform\Facades\ResourceGate */
        $this->app->singleton('ResourceGate', ApiResourceManager::class);

        if (app()->runningUnitTests()) {
            Relation::morphMap([
                'test' => \MetaFox\Platform\Tests\Mock\Models\ContentModel::class, // issuer installation
            ]);
        }

        $this->registerSqlLog();

        /*
         * Some vendors has been integrated to Laravel via composer, so it will be run before AppServiceProvider run
         * So we need to register all package providers via booting callback of application
        */
        $this->app->booting(function () {
            $this->discoverPackageProviders();
        });

        $this->booting(function () {
            $this->discoverPackageViews();
        });
    }

    protected function discoverPackageProviders(): void
    {
        $packages = config('metafox.packages');

        if (!$packages) {
            return;
        }

        $providers = [];

        foreach ($packages as $package) {
            foreach ($package['providers'] as $provider) {
                $providers[] = $provider;
            }
        }

        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    private function discoverPackageViews(): void
    {
        View::addNamespace('mail', resource_path('views/vendor/mail/html'));

        PackageManager::with(function ($package, $info) {
            if (is_dir($viewPath = base_path($info['path'] . '/resources/views'))) {
                View::addNamespace($info['alias'], $viewPath);
            }
        });
    }

    public function registerSqlLog()
    {
        if (!config('database.enableSqlLog')) {
            return;
        }

        $prefix = uniqid('request_');

        DB::listen(function ($query) use ($prefix) {
            $sql = $query->sql;
            foreach ($query->bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'{$binding}'";
                } elseif ($binding === null) {
                    $binding = 'NULL';
                } elseif ($binding instanceof Carbon) {
                    $binding = "'{$binding->toDateTimeString()}'";
                } elseif ($binding instanceof DateTime) {
                    $binding = "'{$binding->format('Y-m-d H:i:s')}'";
                }

                $sql = preg_replace("/\?/", $binding, $sql, 1);
            }

            Log::channel('sql')->debug($prefix . ' ' . $sql);
        });
    }
}
