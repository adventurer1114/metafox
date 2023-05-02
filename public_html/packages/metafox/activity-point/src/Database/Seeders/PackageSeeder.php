<?php

namespace MetaFox\ActivityPoint\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Auth\Access\Gate;
use Illuminate\Database\Seeder;
use MetaFox\ActivityPoint\Models\PointSetting;
use MetaFox\ActivityPoint\Repositories\PointSettingRepositoryInterface;
use MetaFox\ActivityPoint\Repositories\PointStatisticRepositoryInterface;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * Class PackageSeeder.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->installSettings();
        $this->seedingStatistics();
    }

    protected function installSettings(): void
    {
        $now         = Carbon::now();
        $defaultData = [
            'points'     => 0,
            'max_earned' => 0,
            'period'     => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        //Seed custom setting register from other app
        ActivityPoint::installCustomPointSettings($defaultData);

        $actions                = PointSetting::POINT_SETTING_ACTIONS;
        $driverRepo             = resolve(DriverRepositoryInterface::class);
        $entities               = $driverRepo->loadDrivers(Constants::DRIVER_TYPE_ENTITY, null);
        $allRoles               = resolve(RoleRepositoryInterface::class)->all()->pluck('id')->toArray();
        $pointSettingRepository = resolve(PointSettingRepositoryInterface::class);
        $insertData             = [];

        foreach ($allRoles as $role) {
            foreach ($entities as $entity) {
                [, $driver,, $alias, $packageId]  = $entity;
                $resource                         = resolve($driver);

                if (!$resource instanceof Content) {
                    continue;
                }

                if (ActivityPoint::isCustomInstalled($packageId)) {
                    continue;
                }

                // todo improve performance ?

                foreach ($actions as $action) {
                    $name        = sprintf('%s.%s', $resource->entityType(), $action);
                    $description = sprintf('activitypoint::phrase.%s_%s_description', $resource->entityType(), $action);

                    $insertData[$role . $name] = array_merge($defaultData, [
                        'name'               => $name,
                        'action'             => $action,
                        'module_id'          => $alias,
                        'role_id'            => $role,
                        'package_id'         => $packageId,
                        'description_phrase' => $description,
                    ]);
                }
            }
        }

        $pointSettingRepository->getModel()->newQuery()->upsert(array_values($insertData), ['name', 'role_id'], ['name', 'role_id', 'description_phrase']);
    }

    private function seedingStatistics(): void
    {
        $users = resolve(UserRepositoryInterface::class)
            ->all();

        foreach ($users as $user) {
            resolve(PointStatisticRepositoryInterface::class)->firstOrCreate(['id' => $user->entityId()]);
        }
    }
}
