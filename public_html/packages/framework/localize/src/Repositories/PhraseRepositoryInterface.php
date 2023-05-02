<?php

namespace MetaFox\Localize\Repositories;

use Illuminate\Validation\ValidationException;
use MetaFox\Localize\Models\Phrase;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Phrase.
 * @mixin BaseRepository
 * @method Phrase find($id, $columns = ['*'])
 * @method Phrase getModel()
 */
interface PhraseRepositoryInterface
{
    /**
     * @param array<string, mixed> $attributes
     *
     * @return Phrase
     * @throws ValidationException
     */
    public function createPhrase(array $attributes): Phrase;

    /**
     * Add multiple phrases by key.
     * @param  array $data
     * @param  bool  $dryRun
     * @return void
     */
    public function updatePhrases(array $data, bool $dryRun = false): void;

    /**
     * Get actual translation in database.
     *
     * @param  string|null $key
     * @param  string|null $locale
     * @return string|null
     */
    public function translationOf(?string $key, string $locale = null): ?string;

    /**
     * Add sample phrases.
     *
     * @param  string      $key
     * @param  string|null $text
     * @param  string|null $locale
     * @param  bool        $dryRun
     * @param  bool        $overwrite Overwrite existing translation
     * @return bool
     */
    public function addSamplePhrase(
        string $key,
        ?string $text,
        ?string $locale,
        bool $dryRun = false,
        bool $overwrite = false
    ): bool;

    /**
     * @param string $key
     * @param string $locale
     *
     * @return bool
     */
    public function checkExistKey(string $key, string $locale): bool;

    /**
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return Phrase
     * @throws ValidationException
     */
    public function updatePhrase(int $id, array $attributes): Phrase;

    /**
     * @param  string $name
     * @param  string $text
     * @return mixed
     */
    public function updateByKey(string $name, string $text);

    /**
     * @param array<string, mixed> $attributes
     *
     * @return mixed
     */
    public function viewPhrases(array $attributes);

    /**
     * Get group options.
     *
     * @return array<string,string>
     */
    public function getGroupOptions(): array;

    /**
     * Get group options.
     *
     * @return array<string,string>
     */
    public function getLocaleOptions(): array;

    /**
     * @param  string $key
     * @param  string $text
     * @param  string $locale
     * @return void
     */
    public function addTranslation(string $key, string $text, string $locale): void;

    /**
     * @param  string $locale
     * @return bool
     */
    public function deletePhrasesByLocale(string $locale): bool;

    /**
     * @param  string $key
     * @return Phrase
     */
    public function getPhrasesByKey(string $key): Phrase;
}
