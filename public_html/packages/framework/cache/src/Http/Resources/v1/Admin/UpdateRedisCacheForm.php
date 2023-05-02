<?php

namespace MetaFox\Cache\Http\Resources\v1\Admin;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use MetaFox\Form\AbstractForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use Nette\Schema\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @driverType form-cache
 * @driverName file
 */
class UpdateRedisCacheForm extends Form
{
    protected function prepare(): void
    {
        $res    = $this->resource ?? [];
        $value  = config('database.redis.cache', []);
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
            Builder::text('host')
                ->required()
                ->label(__p('cache::redis.host_label')),
            Builder::text('port')
                ->required()
                ->label(__p('cache::redis.port_label')),
            Builder::text('database')
                ->required()
                ->label(__p('cache::redis.database_label')),
            Builder::text('username')
                ->optional()
                ->label(__p('core::phrase.username')),
            Builder::text('password')
                ->optional()
                ->label(__p('cache::redis.password_label')),
        );

        $this->addDefaultFooter(true);
    }

    /**
     * @param  Request                  $request
     * @return array
     * @throws InvalidArgumentException
     */
    public function validated(Request $request): array
    {
        $connectionConfig = $request->validate([
            'host'     => 'string|required',
            'port'     => 'string|sometimes|nullable',
            'database' => 'string|sometimes|nullable',
            'username' => 'string|sometimes|nullable',
            'password' => 'string|sometimes|nullable',
        ]);

        config()->set('database.redis.cache', $connectionConfig);

        $this->validateCacheConfiguration($connectionConfig);

        $data['driver']     = 'redis';
        $data['connection'] = 'cache';
        $data['label']      = 'redis';

        Settings::updateSetting(
            'cache',
            'cache.redis',
            'database.redis.cache',
            null,
            $connectionConfig,
            'array',
            false,
            true
        );

        return $data;
    }

    /**
     * @param  array                                        $config
     * @return void
     * @throws InvalidArgumentException|ValidationException
     */
    public function validateCacheConfiguration(array $config): void
    {
        try {
            $store = Cache::store('redis');
            $key   = __METHOD__;
            $value = Carbon::now();
            $store->set($key, $value);
            $store->get($key);
        } catch (Exception) {
            throw new \InvalidArgumentException('Could not save item to cache store');
        }
    }
}
