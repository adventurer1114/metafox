<?php

namespace MetaFox\Localize\Contracts;

use MetaFox\Localize\Models\Language as Model;

/**
 * Interface LanguageSupportContract.
 */
interface LanguageSupportContract
{
    /**
     * @return array<string, Model>
     */
    public function getLanguages(): array;

    /**
     * @param string $languageId
     *
     * @return Model|null
     */
    public function getLanguage(string $languageId): ?Model;

    /**
     * @return array<string, Model>
     */
    public function getAllActiveLanguages(): array;

    /**
     * @return array<array<string, mixed>>
     */
    public function getActiveOptions(): array;

    /**
     * @param string|null $code
     *
     * @return string|null
     */
    public function getName(?string $code): ?string;

    /**
     * @return array<string>
     */
    public function availableLocales(): array;
}
