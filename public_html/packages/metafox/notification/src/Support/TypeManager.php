<?php

namespace MetaFox\Notification\Support;

use Illuminate\Support\Arr;
use MetaFox\Notification\Contracts\TypeManager as TypeManagerContract;
use MetaFox\Notification\Models\ModuleSetting;
use MetaFox\Notification\Models\Notification;
use MetaFox\Notification\Models\NotificationModule;
use MetaFox\Notification\Models\NotificationSetting;
use MetaFox\Notification\Models\Type;
use MetaFox\Notification\Models\TypeChannel;

/**
 * Class TypeManager.
 */
class TypeManager implements TypeManagerContract
{
    /**
     * @var mixed
     */
    private $types;

    public function refresh(): void
    {
        cache()->delete(CacheManager::NOTIFICATION_TYPE_CACHE);
        $this->types = null;
    }

    /**
     * Create or update an notification type.
     * Note: this method won't purge cache. Please purge cache manually.
     *
     * @param array<string, mixed> $data
     */
    public function makeType(array $data): void
    {
        $isType = Type::query()
            ->where('type', '=', $data['type'])
            ->exists();

        if ($isType) {
            /** @var Type $query */
            $query    = Type::query()->where('type', '=', $data['type'])->first();
            $ordering = Arr::get($data, 'ordering', 1);
            $channels = Arr::get($data, 'channels');

            $this->handleMakeData($channels, $query, $ordering);
            $query->update($data);

            return;
        }
        $type = new Type();

        $defaultData = [
            'can_edit'   => 1,
            'is_request' => 0,
            'is_system'  => 0,
            'ordering'   => 1,
        ];

        $data = array_merge($defaultData, $data);

        $type->fill([
            'type'       => $data['type'],
            'title'      => $data['title'] ?? $data['type'],
            'handler'    => $data['handler'] ?? '',
            'module_id'  => $data['module_id'],
            'can_edit'   => $data['can_edit'],
            'is_request' => $data['is_request'],
            'is_system'  => $data['is_system'],
            'ordering'   => $data['ordering'],
        ])->save();

        if (!empty($data['channels'])) {
            $this->handleMakeData($data['channels'], $type, $data['ordering']);
        }
    }

    protected function handleMakeData(array $channels, Type $type, int $ordering): void
    {
        if (!isset($type->handler)) {
            return;
        }
        foreach ($channels as $channel) {
            $this->makeTypeChannel($type->entityId(), $channel, $ordering);
            $this->makeModule($type->module_id, $channel);
        }
    }

    public function isActive(string $type): bool
    {
        return isset($this->types[$type]);
    }

    public function getTypePhrase(string $type): ?string
    {
        if (!$this->isActive($type)) {
            return null;
        }

        $text = $this->types[$type]['title'];

        if (!is_string($text)) {
            return null;
        }

        return __p($text);
    }

    public function hasSetting(string $type, string $feature): bool
    {
        if (!$this->isActive($type)) {
            return false;
        }

        if (!isset($this->types[$type])) {
            return false;
        }

        return in_array($feature, $this->types[$type]);
    }

    protected function makeTypeChannel(int $typeId, string $channel, int $ordering): void
    {
        $typeChannel = new TypeChannel();

        $isExists = $typeChannel->newQuery()->where([
            'type_id' => $typeId,
            'channel' => $channel,
        ])->exists();

        if ($isExists) {
            return;
        }

        $typeChannel->fill([
            'type_id'  => $typeId,
            'channel'  => $channel,
            'ordering' => $ordering,
        ])->save();
    }

    protected function makeModule(string $module, string $channel): void
    {
        $model    = new NotificationModule();
        $isExists = $model->newQuery()
            ->where('module_id', $module)
            ->where('channel', $channel)
            ->exists();

        if ($isExists) {
            return;
        }

        $model->fill([
            'module_id' => $module,
            'title'     => $module . '::phrase.' . $module . '_notification_type',
            'channel'   => $channel,
        ])->save();
    }

    public function clearNotificationSettings(array $data): void
    {
        NotificationSetting::query()->delete();
    }

    public function handleDeletedModuleId(array $data): void
    {
        $model   = new NotificationModule();
        $modules = $model->newQuery()->whereIn('module_id', $data);
        $getIds  = $modules->pluck('id')->toArray();

        ModuleSetting::query()->whereIn('module_id', $getIds)->delete();
        $modules->delete();
    }

    public function handleDeletedTypeByName(array $data): void
    {
        $types = Type::query()->whereIn('type', $data)->get();

        Notification::query()->whereIn('type', $data)->delete();

        foreach ($types as $type) {
            /** @var Type $type */
            $getIds = $type->typeChannels()->pluck('id')->toArray();

            NotificationSetting::query()->whereIn('type_id', $getIds)->delete();
            $type->typeChannels()->delete();
            $type->delete();
        }
    }
}
