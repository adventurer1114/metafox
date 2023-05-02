<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Support\Browse\Scopes\Listing\ViewScope;
use MetaFox\Platform\Resource\MobileSetting as Setting;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;

/**
 *--------------------------------------------------------------------------
 * Listing Mobile Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class MobileSetting.
 * @ignore
 * @codeCoverageIgnore
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('homePage')
            ->apiUrl('marketplace');

        $this->add('searchItem')
            ->apiUrl('marketplace')
            ->apiParams([
                'q'           => ':q',
                'sort'        => ':sort',
                'when'        => ':when',
                'view'        => 'search',
                'category_id' => ':category_id',
                'country_iso' => ':country_iso',
            ])
            ->placeholder(__p('marketplace::phrase.search_marketplace'));

        $this->add('viewAll')
            ->apiUrl('marketplace')
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'sort' => [
                    'includes',
                    'sort',
                    SortScope::getAllowSort(),
                ],
                'category_id' => ['numeric', 'category_id'],
                'when'        => [
                    'includes',
                    'when',
                    WhenScope::getAllowWhen(),
                ],
                'view' => [
                    'includes',
                    'view',
                    ViewScope::getAllowView(),
                ],
                'country_iso' => ['truthy', 'country_iso'],
            ])
            ->apiParams(['view' => ViewScope::VIEW_ALIVE]);

        $this->add('viewFriendsListings')
            ->apiUrl('marketplace')
            ->apiParams(['view' => Browse::VIEW_FRIEND]);

        $this->add('viewMyListings')
            ->apiUrl('marketplace')
            ->apiParams(['view' => Browse::VIEW_MY]);

        $this->add('viewMyExpiredListings')
            ->apiUrl('marketplace')
            ->apiParams(['view' => ViewScope::VIEW_EXPIRE, 'user_id' => ':authId']);

        $this->add('viewListingInvites')
            ->apiUrl('marketplace')
            ->apiParams(['view' => ViewScope::VIEW_INVITE]);

        $this->add('viewExpired')
            ->apiUrl('marketplace')
            ->apiParams(['view' => ViewScope::VIEW_EXPIRE]);

        $this->add('viewHistory')
            ->apiUrl('marketplace')
            ->apiParams(['view' => ViewScope::VIEW_HISTORY]);

        $this->add('viewPendingListings')
            ->apiUrl('marketplace')
            ->apiParams(['view' => Browse::VIEW_PENDING]);

        $this->add('viewMyPendingListings')
            ->apiUrl('marketplace')
            ->apiParams(['view' => Browse::VIEW_MY_PENDING]);

        $this->add('viewInvoices')
            ->apiUrl('marketplace-invoice');

        $this->add('viewItem')
            ->apiUrl('marketplace/:id')
            ->pageUrl('marketplace/:id');

        $this->add('deleteItem')
            ->apiUrl('marketplace/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('marketplace::phrase.delete_confirm'),
                ]
            );

        $this->add('addItem')
            ->pageUrl('marketplace/add')
            ->apiUrl('core/mobile/form/marketplace.store')
            ->apiParams(['owner_id' => ':id']);

        $this->add('editItem')
            ->pageUrl('marketplace/edit/:id')
            ->apiUrl('core/mobile/form/marketplace.update/:id');

        $this->add('sponsorItem')
            ->apiUrl('marketplace/sponsor/:id');

        $this->add('sponsorItemInFeed')
            ->apiUrl('marketplace/sponsor-in-feed/:id');

        $this->add('featureItem')
            ->apiUrl('marketplace/feature/:id');

        $this->add('approveItem')
            ->apiUrl('marketplace/approve/:id')
            ->asPut();

        $this->add('paymentItem')
            ->apiUrl('core/mobile/form/marketplace.payment/:id')
            ->asGet();

        $this->add('invitePeopleToCome')
            ->apiUrl('marketplace-invite')
            ->asPost()
            ->apiParams([
                'listing_id' => ':id',
                'user_ids'   => ':ids',
            ]);

        $this->add('suggestFriends')
            ->apiUrl('friend/invite-to-item')
            ->asGet()
            ->apiParams([
                'q'         => ':q',
                'owner_id'  => ':owner_id',
                'item_type' => Listing::ENTITY_TYPE,
                'item_id'   => ':id',
            ]);

        $this->add('viewInvitedPeople')
            ->apiUrl('marketplace-invite/invited-people')
            ->apiParams([
                'listing_id' => ':id',
            ]);

        $this->add('searchGlobalListing')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'                        => 'marketplace',
                'q'                           => ':q',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
            ]);

        $this->add('reopenItem')
            ->apiUrl('marketplace/reopen/:id')
            ->asPatch();
    }
}
