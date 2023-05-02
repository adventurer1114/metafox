<?php

namespace App\Providers;

use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\ModuleManager;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use MetaFox\Platform\PackageManager;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<string, string>
     */
    protected $policies = [];

    /**
     * Register any authentication/authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
//        Gate::before(function ($user) {
//            if ($user->hasRole(UserRole::SUPER_ADMIN_USER)) {
//                return true;
//            }

            //Todo: TBD.
//            if ($user->hasRole(UserRole::BANNED_USER)) {
//                return false;
//            }

//            return null;
//        });
    }

    public function register()
    {
        $this->booting(function () {
            $this->discoverPackagePolicies();
        });
    }

    protected function discoverPackagePolicies(): void
    {
        try {
            $repository = resolve(DriverRepositoryInterface::class);
            $policies = $repository->loadDrivers(Constants::DRIVER_TYPE_POLICY_RESOURCE, null, true, null);

            $rules = $repository->loadDrivers(Constants::DRIVER_TYPE_POLICY_RULE, null, true, null);

            foreach ($policies as $policy) {
                $this->policies[$policy[0]] = $policy[1];
                PolicyGate::addPolicy($policy[0], $policy[1]);
            }

            foreach ($rules as $rule) {
                PolicyGate::addRule($rule[0], $rule[1]);
            }
        } catch (\Exception) {
            // missing installed value
        }
    }
}
