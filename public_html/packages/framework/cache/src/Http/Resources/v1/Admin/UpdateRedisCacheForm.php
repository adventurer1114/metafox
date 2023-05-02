<?php

namespace MetaFox\Cache\Http\Resources\v1\Admin;

use Illuminate\Http\Request;
use MetaFox\Form\AbstractForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Yup\Yup;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @driverType form-cache
 * @driverName file
 */
class UpdateRedisCacheForm extends Form
{
    use TraitValidateCacheConfiguration;

    protected function prepare(): void
    {
        $res = $this->resource ?? [];
        $value = $res['value'] ?? [];
        $action = apiUrl('admin.cache.store.update', ['driver' => 'redis', 'name' => $res['name']]);

        $this->title(__p('cache::phrase.edit_cache_store'))
            ->action($action)
            ->asPut()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::choice('connection')
                ->required()
                ->label(__p('cache::redis.connection_label'))
                ->description(__p('cache::redis.connection_desc'))
                ->options($this->getRedisConnectionOptions())
                ->yup(Yup::string()->required()),
        );

        $this->addDefaultFooter(true);
    }

    /**
     * @param  Request  $request
     * @return array
     * @throws InvalidArgumentException
     */
    public function validated(Request $request): array
    {
        $data = $request->validate([
            'connection' => 'string|required',
        ]);

        $data['driver'] = 'redis';

        $this->validateCacheConfiguration($data);

        return $data;
    }

    private function getRedisConnectionOptions(): array
    {
        $ids = array_keys(config('database.redis') ?? []);

        sort($ids);

        $excludes = ['client', 'options', 'queue', 'session'];
        $options = [];

        foreach ($ids as $id) {
            if (in_array($id, $excludes)) {
                continue;
            }

            $options[] = ['value' => $id, 'label' => $id];
        }

        return $options;
    }
}
