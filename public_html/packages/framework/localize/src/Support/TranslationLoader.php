<?php

namespace MetaFox\Localize\Support;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Translation\FileLoader;
use MetaFox\Localize\Models\Phrase;

/**
 * Class TranslationLoader.
 */
class TranslationLoader extends FileLoader
{
    /** @var string */
    private string $fallbackLocale = 'en';

    public function load($locale, $group, $namespace = null)
    {
        $fallbackLocale = $this->fallbackLocale;

        return localCacheStore()->rememberForever(
            "locale.phrases.{$namespace}.{$locale}.{$group}",
            function () use ($fallbackLocale, $locale, $group, $namespace) {
                return $this->loadPhrases($fallbackLocale, $locale, $group, $namespace);
            }
        );
    }

    private function loadPhrases($fallbackLocale, $locale, $group, $namespace): array
    {
        return $locale === $fallbackLocale ? $this->getGroupOfBaseLocale($locale, $group, $namespace) :
            $this->getGroupOfDerivedLocale($locale, $fallbackLocale, $group, $namespace);
    }

    private function getGroupOfBaseLocale(string $locale, string $group, ?string $namespace): array
    {
        $result = [];
        $wheres = [
            ['locale', '=', $locale],
        ];

        if ($group) {
            $wheres[] = ['group', '=', $group];
        }
        if ($namespace) {
            $wheres[] = ['namespace', '=', $namespace];
        }

        $query = Phrase::query()
            ->select(['text', 'name'])
            ->where($wheres);

        foreach ($query->cursor() as $item) {
            Arr::set($result, $item->name, __translation_wrapper($item->text));
        }

        return $result;
    }

    private function getGroupOfDerivedLocale(
        string $locale,
        string $fallbackLocale,
        string $group,
        ?string $namespace
    ): array {
        $result = [];
        $wheres = [
            ['base.locale', '=', $fallbackLocale],
        ];

        if ($group) {
            $wheres[] = ['base.group', '=', $group];
        }
        if ($namespace) {
            $wheres[] = ['base.namespace', '=', $namespace];
        }

        $query = Phrase::query()
            ->from('phrases', 'base')
            ->select([
                'base.key', 'base.locale', 'base.name', 'base.group', 'base.namespace', 'base.text as base_text',
                'derived.text',
            ])
            ->leftJoin('phrases as derived', function (JoinClause $join) use ($locale) {
                $join->on('base.key', '=', 'derived.key');
                $join->where('derived.locale', $locale);

                return $join;
            })
            ->where($wheres);

        foreach ($query->cursor() as $item) {
            Arr::set($result, $item->name, __translation_wrapper($item->text ?? $item->base_text));
        }

        return $result;
    }
}
