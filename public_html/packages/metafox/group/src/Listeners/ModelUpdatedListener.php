<?php

namespace MetaFox\Group\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Core\Models\SiteSetting;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;

class ModelUpdatedListener
{
    public function __construct(protected UserPrivacyRepositoryInterface $repository)
    {
    }

    public function handle(Model $model): void
    {
        $setting = 'group.default_item_privacy';

        if (!$model instanceof SiteSetting) {
            return;
        }
        if ($model->name != $setting) {
            return;
        }
        $value = $model->value_actual ?? $model->value_default;
        $this->repository->updatePrivacyResourceValueByEntity('group', $value);
    }
}
