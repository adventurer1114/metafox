<?php

namespace MetaFox\HealthCheck\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\ModuleManager;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\HealthCheck\Http\Controllers\Api\CheckAdminController::$controllers;.
 */

/**
 * Class CheckAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class CheckAdminController extends ApiController
{
    public function overview()
    {
        Artisan::call('metafox:health-check');

        $message = Artisan::output();

        return $this->success([
            'title' => 'System Overview',
            'items' => [
                ['label' => $message],
            ],
        ]);
    }

    public function wizard()
    {
        $checkers = Arr::flatten(ModuleManager::instance()->discoverSettings('getCheckers'));

        $steps = [];
        foreach ($checkers as $className) {
            /** @var Checker $checker */
            $checker = resolve($className);

            $steps[] = [
                'title'        => $checker->getName(),
                'dryRun'       => true,
                'disableRetry' => true,
                'enableReport' => true,
                'dataSource'   => [
                    'apiUrl'    => '/admincp/health-check/check',
                    'apiMethod' => 'POST',
                ],
                'data' => [
                    'id' => $checker::class,
                ],
            ];
        }

        return $this->success([
            'title'     => 'Health Check',
            'component' => 'ui.step.processes',
            'props'     => [
                'disableNavigateConfirm' => true,
                'steps'                  => $steps,
            ],

        ]);
    }

    public function check(Request $request)
    {
        $id = $request->get('id');

        if (!$id || !class_exists($id)) {
            return $this->success([]);
        }

        $ref = new \ReflectionClass($id);
        if (!$ref->isSubclassOf(Checker::class)) {
            return $this->success([]);
        }

        /** @var Checker $checker */
        $checker = $ref->newInstance();

        $result = $checker->check();

        $message = view('health-check::checker/step-report', [
            'reports' => $result->getReports(),
        ])->render();

        if ($result->okay()) {
            return $this->success(compact('message'));
        }

        return $this->error($message);
    }
}
