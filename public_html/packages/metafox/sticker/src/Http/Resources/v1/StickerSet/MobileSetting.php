<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Sticker\Http\Resources\v1\StickerSet;

use MetaFox\Platform\Resource\MobileSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * Saved Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl(apiUrl('sticker.sticker-set.index'));

        $this->add('viewMyStickerSet')
            ->apiUrl(apiUrl('sticker.sticker-set.index'))
            ->apiParams(['view' => 'my']);

        $this->add('addToMyList')
            ->apiUrl(apiUrl('sticker.sticker-set.user.store'));

        $this->add('removeFromMyList')
            ->apiUrl(apiUrl('sticker.sticker-set.user.destroy', ['id' => ':id']));
    }
}
