<?php

namespace MetaFox\Notification\Contracts;

interface TypeManager
{
    public function hasSetting(string $type, string $feature): bool;

    public function refresh(): void;
}
