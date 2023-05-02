<?php

namespace MetaFox\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;
use MetaFox\Localize\Contracts\LanguageSupportContract;
use MetaFox\Localize\Support\Language as LanguageSupport;

/**
 * class Language.
 * @method static array         getActiveOptions()
 * @method static string|null   getName(?string $id)
 * @method static array<string> availableLocales()
 * @method        static        getDefaultLocaleId()
 * @see LanguageSupport
 */
class Language extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LanguageSupportContract::class;
    }
}
