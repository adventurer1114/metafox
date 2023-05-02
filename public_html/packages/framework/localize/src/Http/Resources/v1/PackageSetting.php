<?php

namespace MetaFox\Localize\Http\Resources\v1;

use MetaFox\Localize\Repositories\LanguageRepositoryInterface;

class PackageSetting
{
    public function getMobileSettings(): array
    {
        return [
            'languages' => resolve(LanguageRepositoryInterface::class)
                ->getOptions(true),
        ];
    }

    public function getWebSettings(): array
    {
        return [
            'languages' => resolve(LanguageRepositoryInterface::class)
                ->getOptions(true),
        ];
    }
}
