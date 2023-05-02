<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Advertise\Http\Resources\v1\Invoice\Admin;

use MetaFox\Platform\MetaFoxForm;
use MetaFox\Platform\Resource\WebSetting as ResourceSetting;

/**
 * stub: /packages/resources/resource_admin_setting.stub
 * Add this class name to resources config gateway.
 */
class WebSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('searchForm')
            ->apiUrl('admincp/core/form/advertise.invoice.search_form');
    }
}
