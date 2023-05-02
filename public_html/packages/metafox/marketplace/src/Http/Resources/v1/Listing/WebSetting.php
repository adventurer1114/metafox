<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Support\Browse\Scopes\Listing\ViewScope;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Resource\WebSetting as Setting;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;

/**
 *--------------------------------------------------------------------------
 * Listing Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class WebSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('homePage')
            ->pageUrl('marketplace');

        $this->add('searchItem')
            ->pageUrl('marketplace/search')
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
            ]);

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
            ->apiUrl('core/form/marketplace.store');

        $this->add('editItem')
            ->pageUrl('marketplace/edit/:id')
            ->apiUrl('core/form/marketplace.update/:id');

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
            ->apiUrl('core/form/marketplace.payment/:id')
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

        $this->add('reopenItem')
            ->apiUrl('marketplace/reopen/:id')
            ->asPatch();

        $this->add('viewEventsMap')
            ->apiUrl('marketplace')
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'sort' => [
                    'includes',
                    'sort',
                    SortScope::getAllowSort(),
                ],
                'when' => [
                    'includes',
                    'when',
                    WhenScope::getAllowWhen(),
                ],
                'limit' => [
                    'includes',
                    'limit',
                    [
                        MetaFoxConstant::VIEW_5_NEAREST,
                        MetaFoxConstant::VIEW_10_NEAREST,
                        MetaFoxConstant::VIEW_15_NEAREST,
                        MetaFoxConstant::VIEW_20_NEAREST,
                    ],
                ],
                'bounds_west'  => ['truthy', 'bounds_west'],
                'bounds_east'  => ['truthy', 'bounds_east'],
                'bounds_south' => ['truthy', 'bounds_south'],
                'bounds_north' => ['truthy', 'bounds_north'],
                'zoom'         => ['truthy', 'zoom'],
            ]);
    }
}
