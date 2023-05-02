<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Photo\Http\Resources\v1\Album;

use MetaFox\Platform\Resource\MobileSetting as ResourceSetting;

/**
 *--------------------------------------------------------------------------
 * Album Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 * @driverType resource-mobile
 * @driverName album
 */
class MobileSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('homePage')
            ->pageUrl('photo');

        $this->add('searchItem')
            ->apiUrl('photo-album')
            ->apiParams([
                'q'    => ':q',
                'sort' => ':sort',
                'when' => ':when',
            ])
            ->placeholder(__p('photo::phrase.search_albums'));

        $this->add('viewAll')
            ->apiUrl('photo-album')
            ->apiRules([
                'q'        => ['truthy', 'q'],
                'sort'     => ['includes', 'sort', ['latest', 'most_viewed', 'most_liked', 'most_discussed']],
                'category' => ['numeric', 'category'],
                'when'     => ['includes', 'when', ['this_month', 'this_week', 'today']],
                'view'     => ['includes', 'view', ['my', 'friend', 'pending']],
            ]);

        $this->add('viewItem')
            ->apiUrl('photo-album/:id')
            ->pageUrl('photo/album/:id');

        $this->add('deleteItem')
            ->apiUrl('photo-album/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('photo::phrase.delete_confirm_album'),
                ]
            );

        $this->add('addItem')
            ->pageUrl('photo/album/add')
            ->apiUrl('core/mobile/form/photo.album.store')
            ->apiParams(['owner_id' => ':id']);

        $this->add('editItem')
            ->pageUrl('photo/album/edit/:id')
            ->apiUrl('core/mobile/form/photo.album.update/:id');

        $this->add('sponsorItem')
            ->apiUrl('photo-album/sponsor/:id');

        $this->add('featureItem')
            ->apiUrl('photo-album/feature/:id');

        $this->add('addItems')
            ->apiUrl('core/mobile/form/photo.album.add_items/:id')
            ->asGet();

        $this->add('getAlbumItems')
            ->apiUrl('photo-album/items/:id')
            ->asGet();

        $this->add('addItemForm')
            ->apiUrl('core/form/photo_album.store/?owner_id=:id')
            ->asGet();

        $this->add('viewMyAlbums')
            ->apiUrl('photo-album')
            ->apiParams([
                'view' => 'my',
            ]);

        $this->add('searchGlobalPhotoAlbum')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'                        => 'photo_album',
                'q'                           => ':q',
                'owner_id'                    => ':owner_id',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
            ]);
    }
}
