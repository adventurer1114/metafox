<?php

namespace MetaFox\Localize\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use MetaFox\Core\Support\CacheManager;
use MetaFox\Localize\Contracts\LanguageSupportContract;
use MetaFox\Localize\Models\Language as Model;
use MetaFox\Platform\Facades\Settings;

class Language implements LanguageSupportContract
{
    /**
     * @var array<string, Model>
     */
    private array $languages;

    public function __construct()
    {
        $this->init();
    }

    public function getCacheName(): string
    {
        return CacheManager::CORE_LANGUAGE_CACHE;
    }

    public function clearCache(): void
    {
        Cache::forget($this->getCacheName());
    }

    public function getLanguage(string $languageId): ?Model
    {
        return $this->languages[$languageId] ?? null;
    }

    /**
     * @return array<string, Model>
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function getAllActiveLanguages(): array
    {
        return Arr::where($this->languages, function (Model $value) {
            return $value->is_active;
        });
    }

    public function getDefaultLocaleId(): string
    {
        return Settings::get('localize.default_locale', 'en');
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function getActiveOptions(): array
    {
        return Cache::rememberForever('Language__activeOptions', function () {
            return array_map(function (Model $item) {
                return ['value' => $item->language_code, 'label' => $item->name];
            }, Model::query()
                ->orderBy('name')
                ->get()
                ->all());
        });
    }

    public function getName(?string $code): ?string
    {
        if (!$code) {
            return null;
        }

        return Cache::rememberForever('language_' . $code, function () use ($code) {
            /** @var ?Model $model */
            $model = Model::query()->where('language_code', '=', $code)->first();

            return $model?->name;
        });
    }

    protected function init(): void
    {
        $this->languages = Cache::remember(
            $this->getCacheName(),
            3000,
            function () {
                return Model::query()
                    ->orderBy('id')
                    ->orderBy('name')
                    ->get()
                    ->keyBy('language_code')
                    ->all();
            }
        );
    }

    /**
     * @return array<string>
     */
    public function availableLocales(): array
    {
        return Cache::rememberForever('language_supported_locales', function () {
            $locale = Model::query()->where('is_active', '=', 1)
                ->get()
                ->pluck('language_code')
                ->toArray();

            if (!is_array($locale)) {
                return [];
            }

            return $locale;
        });
    }
}
