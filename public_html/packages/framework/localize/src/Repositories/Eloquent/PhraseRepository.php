<?php

namespace MetaFox\Localize\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use MetaFox\Localize\Models\Phrase;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Helper\Pagination;

/**
 * @method Phrase find($id, $columns = ['*'])
 * @method Phrase getModel()
 */
class PhraseRepository extends AbstractRepository implements PhraseRepositoryInterface
{
    public function model(): string
    {
        return Phrase::class;
    }

    public function translationOf(?string $key, string $locale = null): ?string
    {
        if (!$key) {
            return null;
        }

        if (!$locale) {
            $locale = config('app.locale', 'en');
        }

        /** @var ?Phrase $model */
        $model = $this->getModel()->newQuery()->where(['key' => $key, 'locale' => $locale])->first();
        if (!$model instanceof Phrase) {
            return null;
        }

        return $model->text ? __translation_wrapper($model->text) : null;
    }

    public function updatePhrases(array $data, bool $dryRun = false): void
    {
        foreach ($data as $key => $text) {
            $this->addSamplePhrase($key, $text, null, false, true);
        }
    }

    public function addSamplePhrase(
        string $key,
        ?string $text = null,
        ?string $locale = null,
        bool $dryRun = false,
        bool $overwrite = false
    ): bool {
        if (!$locale) {
            $locale = config('app.locale', 'en');
        }

        [$namespace, $group, $name] = app('translator')->parseKey($key);

        if ($group === '*') {
            return false;
        }
        if ($namespace === '*') {
            return false;
        }

        if ($dryRun) {
            return true;
        }

        if (!$overwrite && $this->checkExistKey($key, $locale)) {
            return false;
        }

        $this->updateOrCreate([
            'locale' => $locale,
            'key'    => $key,
        ], [
            'namespace'  => $namespace,
            'group'      => $group,
            'key'        => $key,
            'name'       => $name,
            'locale'     => $locale,
            'package_id' => PackageManager::getByAlias($namespace),
            'text'       => $text ?? sprintf('[%s]', $name),
        ]);

        return true;
    }

    public function createPhrase(array $attributes): Phrase
    {
        $name                    = $attributes['name'];
        $group                   = $attributes['group'];
        $namespace               = $attributes['package_id'] ?? 'core';
        $locale                  = $attributes['locale'];
        $attributes['namespace'] = $namespace;
        $attributes['key']       = toTranslationKey($namespace, $group, $name);

        if ($this->checkExistKey($attributes['key'], $locale)) {
            throw ValidationException::withMessages([
                'key' => __p('core::validation.the_attribute_already_existed', ['attribute' => 'key']),
            ]);
        }

        $phrase = new Phrase($attributes);
        $phrase->save();
        Artisan::call('cache:reset');

        return $phrase;
    }

    public function checkExistKey(string $key, string $locale): bool
    {
        return $this->getModel()->newQuery()
            ->where('key', $key)
            ->where('locale', $locale)
            ->exists();
    }

    public function updatePhrase(int $id, array $attributes): Phrase
    {
        /** @var Phrase $phrase */
        $phrase = $this->find($id);

        // check value is changed.
        if ($phrase->text !== $attributes['text']) {
            $phrase->is_modified = 1;
        }

        $phrase->fill($attributes)->save();
        Artisan::call('cache:reset');

        return $phrase;
    }

    public function updateByKey(string $name, string $text)
    {
        $this->findByField('key', $name);
    }

    public function viewPhrases(array $attributes)
    {
        $query = $this->getModel()->newQuery();

        if ($q = $attributes['q'] ?? null) {
            $query = $query->addScope(new SearchScope($q, ['key', 'text']));
        }

        if ($group = $attributes['group'] ?? null) {
            $query->where('group', '=', $group);
        }

        if ($locale = $attributes['locale'] ?? config('app.locale')) {
            $query->where('locale', '=', $locale);
        }

        if ($namespace = $attributes['namespace'] ?? null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($package = $attributes['package_id'] ?? null) {
            $query->where('package_id', '=', $package);
        }

        return $query->paginate($attributes['limit'] ?? Pagination::DEFAULT_ITEM_PER_PAGE);
    }

    public function getGroupOptions(): array
    {
        /** @var Collection<Phrase> $data */
        $data = $this->getModel()->newQuery()->select('group')->distinct()->get();

        return $data->map(function (Phrase $item) {
            return ['label' => $item->group, 'value' => $item->group];
        })->toArray();
    }

    public function getLocaleOptions(): array
    {
        /** @var Collection<Phrase> $data */
        $data = $this->getModel()->newQuery()->select('locale')->distinct()->get();

        return $data->map(function ($item) {
            return ['label' => $item->locale, 'value' => $item->locale];
        })->toArray();
    }

    public function addTranslation(string $key, string $text, string $locale): void
    {
        [$namespace, $group, $name] = app('translator')->parseKey($key);
        $packageId                  = PackageManager::getByAlias($namespace);

        /** @var Phrase $obj */
        $obj = $this->getModel()->newQuery()
            ->firstOrNew([
                'key'    => $key,
                'locale' => $locale,
            ], [
                'namespace'  => $namespace,
                'group'      => $group,
                'name'       => $name,
                'locale'     => $locale,
                'package_id' => $packageId ? $packageId : 'metafox/core',
            ]);

        $obj->text = $text;
        $obj->save();
    }

    public function findDuplicatedPhrases(): array
    {
        $rows = DB::select(DB::raw("SELECT text, count(text) FROM phrases where text <> '' and locale='en' and package_id='metafox/blog' group by text having count(text) > 1"));

        $array = array_map(function ($row) {
            return $row->text;
        }, $rows);

        return $array;
    }

    public function deletePhrasesByLocale(string $locale): bool
    {
        return $this->getModel()
            ->newModelQuery()
            ->where('locale', '=', $locale)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function getPhrasesByKey(string $key): Phrase
    {
        return $this->getModel()
            ->newQuery()
            ->where('key', '=', $key)
            ->first();
    }
}
