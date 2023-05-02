<?php

namespace MetaFox\Notification\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use MetaFox\Notification\Contracts\TypeManager;
use MetaFox\Notification\Models\ModuleSetting;
use MetaFox\Notification\Models\NotificationModule;
use MetaFox\Notification\Models\NotificationSetting;
use MetaFox\Notification\Models\Type;
use MetaFox\Notification\Policies\TypePolicy;
use MetaFox\Notification\Repositories\NotificationModuleRepositoryInterface;
use MetaFox\Notification\Repositories\TypeChannelRepositoryInterface;
use MetaFox\Notification\Repositories\TypeRepositoryInterface;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\PackageScope;

/**
 * Class TypeRepository.
 * @ignore
 * @codeCoverageIgnore
 */
class TypeRepository extends AbstractRepository implements TypeRepositoryInterface
{
    public function model(): string
    {
        return Type::class;
    }

    public function viewTypes(): Collection
    {
        return $this->getModel()->query()
            ->addScope(resolve(PackageScope::class, [
                'table' => $this->getModel()->getTable(),
            ]))
            ->get();
    }

    protected function typeChannelRepository(): TypeChannelRepositoryInterface
    {
        return resolve(TypeChannelRepositoryInterface::class);
    }

    protected function moduleRepository(): NotificationModuleRepositoryInterface
    {
        return resolve(NotificationModuleRepositoryInterface::class);
    }

    public function updateType(User $context, int $id, array $attributes): Type
    {
        /** @var Type $resource */
        $resource = $this->find($id);

        policy_authorize(TypePolicy::class, 'update', $context, $resource);

        $resource->fill($attributes);

        $resource->save();

        $typeManager = resolve(TypeManager::class);
        $typeManager->refresh();

        return $resource;
    }

    public function deleteType(User $context, int $id): int
    {
        $resource = $this->find($id);
        policy_authorize(TypePolicy::class, 'delete', $context, $resource);

        $response = $this->delete($id);

        $typeManager = resolve(TypeManager::class);
        $typeManager->refresh();

        return $response;
    }

    /**
     * @param  User              $context
     * @param  string            $channel
     * @return array<int, mixed>
     */
    public function getNotificationSettingsByChannel(User $context, string $channel): array
    {
        $dataModuleTypes = [];
        $typeChannels    = $this->typeChannelRepository()->getTypesByChannel($channel);
        $moduleChannels  = $this->moduleRepository()->getModulesByChannel($channel);

        $userModuleValues = ModuleSetting::query()
            ->where('user_id', $context->entityId())
            ->get()->pluck([], 'module_id')->toArray();

        foreach ($moduleChannels as $module) {
            $value = isset($userModuleValues[$module->entityId()])
                ? $userModuleValues[$module->entityId()]['user_value']
                : $module->is_active;

            /** @var NotificationModule $module */
            $dataModuleType = [
                'app_name'  => __p($module->module_id . '::phrase.' . $module->module_id),
                'module_id' => $module->module_id,
                'phrase'    => __p($module->title),
                'value'     => $value,
                'channel'   => $module->channel,
            ];
            $dataModuleTypes[] = $this->handleGetTypeSettings($context, $typeChannels, $dataModuleType);
        }

        return $dataModuleTypes;
    }

    public function hasPermissionToSendMail(IsNotifiable $context, string $notificationType): bool
    {
        $type = $this->getModel()->newModelQuery()
            ->where([
                'type' => $notificationType,
            ])
            ->first();

        if (null === $type) {
            return false;
        }

        $setting = NotificationSetting::query()
            ->where([
                'type_id'   => $type->entityId(),
                'user_id'   => $context->entityId(),
                'user_type' => $context->entityType(),
            ])
            ->first();

        /*
         * In case record does not exists, default is true
         */
        if (null === $setting) {
            return true;
        }

        return (bool) $setting->user_value;
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function updateNotificationSettingsByChannel(User $context, array $attributes): bool
    {
        $module = Arr::get($attributes, 'module_id');
        $type   = Arr::get($attributes, 'var_name');
        if ($module !== null) {
            $this->handleUpdateModuleSetting($context, $attributes);

            return true;
        }

        if ($type !== null) {
            $this->handleUpdateTypeSetting($context, $attributes);

            return true;
        }

        return false;
    }

    /**
     * @throws ValidationException
     */
    protected function handleUpdateTypeSetting(User $context, array $attributes): void
    {
        $channel      = Arr::get($attributes, 'channel');
        $type         = Arr::get($attributes, 'var_name');
        $value        = Arr::get($attributes, 'value', 1);
        $typeChannels = $this->typeChannelRepository()->getTypesByChannel($channel);
        $types        = collect($typeChannels)->pluck([], 'type.type')->toArray();

        $this->validateNotificationSettings($types, $type);

        NotificationSetting::query()->updateOrCreate([
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'type_id'   => $types[$type]['id'],
        ], [
            'user_id'    => $context->entityId(),
            'user_type'  => $context->entityType(),
            'type_id'    => $types[$type]['id'],
            'user_value' => $value,
        ]);
    }

    /**
     * @throws ValidationException
     */
    protected function handleUpdateModuleSetting(User $context, array $attributes): void
    {
        $channel        = Arr::get($attributes, 'channel');
        $module         = Arr::get($attributes, 'module_id');
        $value          = Arr::get($attributes, 'value', 1);
        $moduleChannels = $this->moduleRepository()->getModulesByChannel($channel);
        $modules        = collect($moduleChannels)->pluck([], 'module_id')->toArray();

        $this->validateNotificationSettings($modules, $module);

        ModuleSetting::query()->updateOrCreate([
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'module_id' => $modules[$module]['id'],
        ], [
            'user_id'    => $context->entityId(),
            'user_type'  => $context->entityType(),
            'module_id'  => $modules[$module]['id'],
            'user_value' => $value,
        ]);
    }

    protected function handleGetTypeSettings(User $context, Collection $typeChannels, array $dataModuleType): array
    {
        $userTypeValues = NotificationSetting::query()
            ->where('user_id', $context->entityId())
            ->get()->pluck([], 'type_id')->toArray();

        foreach ($typeChannels as $typeChannel) {
            /** @var Type $type */
            $type = $typeChannel->type;
            if (empty($type)) {
                continue;
            }

            if ($dataModuleType['module_id'] == $type->module_id) {
                $value = isset($userTypeValues[$typeChannel->entityId()])
                    ? $userTypeValues[$typeChannel->entityId()]['user_value']
                    : $type->is_active;

                $dataModuleType['type'][] = [
                    'var_name' => $type->type,
                    'phrase'   => __p($type->title),
                    'value'    => (int) $value,
                    'channel'  => $typeChannel->channel,
                ];
            }
        }

        return $dataModuleType;
    }

    /**
     * @param  string[]            $settings
     * @param  string              $type
     * @return void
     * @throws ValidationException
     */
    private function validateNotificationSettings(array $settings, string $type)
    {
        if (!isset($settings[$type])) {
            throw ValidationException::withMessages([
                __p(
                    'notification::phrase.notification_setting_not_exist',
                    ['attribute' => $type]
                ),
            ]);
        }
    }

    public function getAllNotificationType(): array
    {
        return $this->getModel()
            ->newModelQuery()
            ->get()
            ->pluck('type')
            ->toArray();
    }
}
