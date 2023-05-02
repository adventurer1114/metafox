<?php

namespace MetaFox\Page\Http\Controllers\Api;

use MetaFox\Platform\Http\Controllers\Api\GatewayController;

/**
 * --------------------------------------------------------------------------
 *  Api Gateway
 * --------------------------------------------------------------------------.
 *
 * This class solves api versioning problem.
 * DO NOT IMPLEMENT ACTION HERE.
 */

/**
 * Class CategoryController.
 */
class PageCategoryAdminController extends GatewayController
{
    /**
     * @var string[]
     */
    protected $controllers = [
        'v1' => v1\PageCategoryAdminController::class,
    ];

    // DO NOT IMPLEMENT ACTION HERE.
}
