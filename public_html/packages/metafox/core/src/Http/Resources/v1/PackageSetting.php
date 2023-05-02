<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Core\Http\Resources\v1;

/**
 | stub: src/Http/Resources/v1/PackageSetting.stub
 */

/**
 * Class PackageSetting.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSetting
{
    /**
     * @return array<string, mixed>
     */
    public function getWebSettings(): array
    {
        return [
            'adminHomePages'   => app('core.packages')->getInternalAdminUrls(),
            'metafox_news_url' => 'https://www.phpfox.com/blog/',
            'offline'          => file_exists(base_path('storage/framework/down')),
        ];
    }
}
