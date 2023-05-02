<?php

namespace MetaFox\Platform\Contracts;

/**
 * Interface BanWord
 * @package MetaFox\Platform\Contracts
 */
interface BanWord
{
    public function clean(?string $string = null): string;

    public function parse(?string $string = null): string;
}
