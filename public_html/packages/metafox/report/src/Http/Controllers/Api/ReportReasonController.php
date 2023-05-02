<?php

namespace MetaFox\Report\Http\Controllers\Api;

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
 * Class ReportReasonController.
 * @ignore
 * @codeCoverageIgnore
 */
class ReportReasonController extends GatewayController
{
    /**
     * @var string[]
     */
    protected $controllers = [
        'v1'   => v1\ReportReasonController::class,
    ];

    // DO NOT IMPLEMENT ACTION HERE.
}
