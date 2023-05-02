<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Localize\Http\Resources\v1\Language\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LanguageItemCollection extends ResourceCollection
{
    public $collects = LanguageItem::class;
}
