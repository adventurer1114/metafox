<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Marketplace\Http\Resources\v1\Invoice;

use MetaFox\Platform\Resource\WebSetting as ResourceSetting;

/**
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */
class WebSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl('marketplace-invoice')
            ->apiRules([])
            ->asGet();

        $this->add('viewItem')
            ->apiUrl('marketplace-invoice/:id')
            ->pageUrl('marketplace/invoice/:id');

        $this->add('changeItem')
            ->apiUrl('marketplace-invoice/change')
            ->asPost()
            ->apiParams([
                'id' => ':id',
            ])
            ->confirm([
                'title'   => __p('marketplace::phrase.change_invoice'),
                'message' => __p('marketplace::phrase.change_invoice_description'),
            ]);

        $this->add('getRepaymentForm')
            ->apiUrl('core/form/marketplace_invoice.payment/:id')
            ->asGet();
    }
}
