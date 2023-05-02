<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\ActivityPoint\Listeners;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use MetaFox\ActivityPoint\Models\PointSetting;
use MetaFox\ActivityPoint\Repositories\PointStatisticRepositoryInterface;
use MetaFox\ActivityPoint\Support\ActivityPoint as PointSupport;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint;
use MetaFox\App\Repositories\PackageRepositoryInterface;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Core\Constants;
use MetaFox\Core\Models\Driver as CoreDriver;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Models\User as UserModel;

/**
 * Class ModelCreatedListener.
 * @ignore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ModelCreatedListener
{
    /**
     * @param Model $model
     */
    public function handle($model): void
    {
        if ($model instanceof Content) {
            $this->handleUpdateActivityPoint($model);
        }

        if ($model instanceof User) {
            $this->creatStatistic($model);
        }

        if ($model instanceof CoreDriver) {
            $this->createPointSetting($model);
        }
    }

    /**
     * @param Content $model
     */
    private function handleUpdateActivityPoint(Content $model): void
    {
        if (!$model instanceof Model) {
            return;
        }

        if ($model->isDraft() || !$model->isApproved()) {
            return;
        }

        ActivityPoint::updateUserPoints($model->user, $model, 'create', PointSupport::TYPE_EARNED);
    }

    private function creatStatistic(User $model): void
    {
        if (!$model instanceof UserModel) {
            return;
        }

        resolve(PointStatisticRepositoryInterface::class)->firstOrCreate(['id' => $model->entityId()]);
    }

    private function createPointSetting(CoreDriver $model): bool
    {
        if (Constants::DRIVER_TYPE_ENTITY !== $model->type) {
            return false;
        }

        $driver = $model->driver;
        /** @var mixed $resource */
        $resource = resolve($driver);
        $module   = resolve(PackageRepositoryInterface::class)->getPackageByName($model->package_id);

        if (!$resource instanceof Content) {
            return false;
        }

        if (ActivityPoint::isCustomInstalled($model->package_id)) {
            return false;
        }

        $now         = Carbon::now();

        $defaultData = [
            'points'     => 0,
            'max_earned' => 0,
            'period'     => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $actions  = PointSetting::POINT_SETTING_ACTIONS;

        $allRoles = resolve(RoleRepositoryInterface::class)
            ->getUsableRoles()
            ->pluck('id')
            ->toArray();

        $insertData = [];

        foreach ($allRoles as $role) {
            foreach ($actions as $action) {
                $name        = sprintf('%s.%s', $resource->entityType(), $action);
                $description = sprintf('activitypoint::phrase.%s_%s_description', $resource->entityType(), $action);

                $insertData[$role . $name] = array_merge($defaultData, [
                    'name'               => $name,
                    'action'             => $action,
                    'module_id'          => $module->alias,
                    'role_id'            => $role,
                    'package_id'         => $model->package_id,
                    'description_phrase' => $description,
                ]);
            }
        }

        PointSetting::query()->upsert(array_values($insertData), ['name', 'role_id'], ['name', 'role_id', 'description_phrase']);

        return true;
    }
}
