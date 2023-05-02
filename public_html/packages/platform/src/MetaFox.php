<?php

namespace MetaFox\Platform;

/**
 * Class PHPFox.
 */
class MetaFox
{
    /**
     * Get the phpFox version.
     *
     * @return string
     */
    public static function getVersion()
    {
        return MetaFoxConstant::VERSION;
    }

    /**
     * Get the phpFox product build.
     *
     * @return string
     */
    public static function getProductBuild()
    {
        return MetaFoxConstant::PRODUCT_BUILD;
    }

    /**
     * Check is trial.
     *
     * @return bool
     */
    public static function isTrial(): bool
    {
        return false;
    }

    public static function isMobile(): bool
    {
        return (bool) request()->headers->get('X-Mobile', false);
    }

    public static function getResolution(): string
    {
        if (self::isMobile()) {
            return MetaFoxConstant::RESOLUTION_MOBILE;
        }

        return MetaFoxConstant::RESOLUTION_WEB;
    }

    /**
     * Define core packages.
     *
     * @return string[]
     */
    public static function coreModules(): array
    {
        return [
            'Privacy',
            'Core',
            'User',
            'Activity',
            'Friend',
            'Photo',
        ];
    }
}
