<?php

namespace MetaFox\Activity\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use MetaFox\Activity\Contracts\TypeManager as TypeManagerContract;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Type;

/**
 * Class TypeManager.
 */
class TypeManager implements TypeManagerContract
{
    /**
     * @var mixed
     */
    private $types;

    /**
     * @var string
     */
    private const CACHE_NAME = 'activity_type_manager_cache';

    /**
     * @var int
     */
    private const CACHE_LIFETIME = 3000;

    public function __construct()
    {
        $this->start();
    }

    protected function start(): void
    {
        if (!$this->types) {
            $this->types = Cache::remember(self::CACHE_NAME, self::CACHE_LIFETIME, function () {
                $data = [];
                /**
                 * @var Type[] $types
                 */
                $types = Type::query()->where(['is_active' => true])->get();
                foreach ($types as $type) {
                    $data[$type->type] = $type->describe();
                }

                return $data;
            });
        }
    }

    public function isActive(string $type): bool
    {
        return isset($this->types[$type]);
    }

    public function hasFeature(string $type, string $feature): bool
    {
        if (!$this->isActive($type)) {
            return false;
        }

        if (!isset($this->types[$type][$feature])) {
            return false;
        }

        return $this->types[$type][$feature];
    }

    public function refresh(): void
    {
        cache()->delete(self::CACHE_NAME);
        $this->types = null;
        $this->start();
    }

    /**
     * Create or update an activity type.
     * Note: this method won't purge cache. Please purge cache manually.
     *
     * @param array<string, mixed> $data
     *
     * @return Type|false
     */
    public function makeType($data)
    {
        $type = Type::query()
            ->where('type', '=', $data['type'])
            ->where('module_id', '=', $data['module_id'])
            ->first();

        if (!$type) {
            $type = new Type();
        }

        $defaultData = [
            'title'       => $data['module_id'],
            'description' => $data['module_id'],
            'is_active'   => 0,
            'is_system'   => 0,
        ];

        $data = array_merge($defaultData, $data);

        $fields = Type::query()
            ->getModel()
            ->getFillable();

        $values = Arr::except($data, $fields);
        $row    = Arr::only($data, $fields);

        $row['value_default'] = $values;

        $type->fill($row);

        return $type->save() ? $type : false;
    }

    public function getTypePhrase(string $type): ?string
    {
        if (!$this->isActive($type)) {
            return null;
        }

        $text = $this->types[$type]['description'];

        if (!is_string($text)) {
            return null;
        }

        return __p($text);
    }

    public function getTypePhraseWithContext(Feed $feed): ?string
    {
        $type = $feed->type_id;

        if (!$this->isActive($type)) {
            return null;
        }

        $text = $this->types[$type]['description'];

        if (!is_string($text)) {
            return null;
        }

        $params = $this->types[$type]['params'];

        if (empty($params)) {
            return __p($text);
        }

        $feedParams = [];

        $dataFlatten = Arr::dot($feed->toArray());

        foreach ($params as $phraseKey => $mappingObjectKey) {
            $feedParams[$phraseKey] = $dataFlatten[$mappingObjectKey] ?? null;
        }

        return __p($text, $feedParams);
    }

    public function hasSetting(string $type, string $feature): bool
    {
        if (!$this->isActive($type)) {
            return false;
        }

        if (!isset($this->types[$type])) {
            return false;
        }

        if (!isset($this->types[$type][$feature])) {
            return false;
        }

        return true;
    }

    public function getTypes(): array
    {
        return $this->types ?: [];
    }

    public function getAbilities(): array
    {
        $type = new Type();

        return $type->getAbilities();
    }

    public function getTypeSettings(): array
    {
        $types = $this->getTypes();

        $abilities = array_keys($this->getAbilities());

        foreach ($types as $key => $type) {
            $only = Arr::only($type, $abilities);

            $only = array_map(function ($value) {
                return (bool) $value;
            }, $only);

            $types[$key] = $only;
        }

        return $types;
    }

    public function cleanData(): void
    {
        Artisan::call('cache:reset');
    }
}
