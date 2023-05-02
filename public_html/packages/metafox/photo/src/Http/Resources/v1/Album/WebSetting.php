<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Photo\Http\Resources\v1\Album;

use MetaFox\Platform\Resource\WebSetting as ResourceSetting;

/**
 *--------------------------------------------------------------------------
 * Album Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */
class WebSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('homePage')
            ->pageUrl('photo');

        $this->add('searchItem')
            ->pageUrl('photo/albums/search')
            ->placeholder(__p('photo::phrase.search_albums'));

        $this->add('viewAll')
            ->apiUrl('photo-album')
            ->apiRules(['q' => ['truthy', 'q'], 'sort' => ['includes', 'sort', ['latest', 'most_viewed', 'most_liked', 'most_discussed']], 'category' => ['numeric', 'category'], 'when' => ['includes', 'when', ['this_month', 'this_week', 'today']], 'view' => ['includes', 'view', ['my', 'friend', 'pending']]]);

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
            ->apiUrl('core/form/photo_album.store');

        $this->add('editItem')
            ->pageUrl('photo/album/edit/:id')
            ->apiUrl('core/form/photo_album.update/:id');

        $this->add('sponsorItem')
            ->apiUrl('photo-album/sponsor/:id');

        $this->add('approveItem')
            ->apiUrl('photo-album/approve/:id')
            ->asPatch();

        $this->add('featureItem')
            ->apiUrl('photo-album/feature/:id');

        $this->add('addPhotos')
            ->apiUrl('core/form/photo_album.add_photos/:id')
            ->asGet();

        $this->add('getAlbumItems')
            ->apiUrl('photo-album/items/:id')
            ->asGet();

        $this->add('addItemForm')
            ->apiUrl('core/form/photo_album.store/?owner_id=:id')
            ->asGet();

        $this->add('selectFromGroupPhotos')
            ->apiUrl('photo')
            ->asGet()
            ->apiParams(['user_id' => ':user_id', 'view' => 'no_album']);

        $this->add('selectFromMyPhotos')
            ->apiUrl('photo')
            ->asGet()
            ->apiParams(['view' => 'no_album']);
    }
}
