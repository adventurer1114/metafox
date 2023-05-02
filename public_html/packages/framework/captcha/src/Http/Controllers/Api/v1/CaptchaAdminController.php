<?php

namespace MetaFox\Captcha\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use MetaFox\Captcha\Http\Requests\v1\Captcha\Admin\IndexRequest;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\MetaFoxConstant;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Captcha\Http\Controllers\Api\CaptchaAdminController::$controllers;
 */

/**
 * Class CaptchaAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class CaptchaAdminController extends ApiController
{
    /**
     * CaptchaAdminController Constructor.
     */
    public function __construct()
    {
    }

    public function index(IndexRequest $request): JsonResponse
    {
        $types = Settings::get('captcha.types', []);

        $data = $this->transformTypes($types);

        return $this->success($data);
    }

    protected function transformTypes(array $types): array
    {
        $drivers = resolve(DriverRepositoryInterface::class)
            ->getNamesHasHandlerClass('form-captcha');

        $data = array_map(function ($config) use ($drivers) {
            $type = Arr::get($config, 'type', MetaFoxConstant::EMPTY_STRING);

            $description = Arr::get($config, 'description', MetaFoxConstant::EMPTY_STRING);

            if (MetaFoxConstant::EMPTY_STRING != $description) {
                $description = __p($description);
            }

            $canEdit = false;

            if (is_array($drivers)) {
                $canEdit = in_array($type, $drivers);
            }

            return [
                'id'          => $type,
                'description' => $description,
                'extra'       => [
                    'can_edit' => $canEdit,
                ],
                'links' => [
                    'editItem' => '/admincp/captcha/type/edit/' . $type,
                ],
            ];
        }, $types);

        $data = array_values($data);

        return $data;
    }

    public function editForm(string $driver): JsonResponse
    {
        $class = resolve(DriverRepositoryInterface::class)
            ->getDriver('form-captcha', $driver, 'admin');

        $form = resolve($class);

        $parameters = [
            'driver' => $driver,
        ];

        if (method_exists($form, 'boot')) {
            app()->call([$form, 'boot'], $parameters);
        }

        return $this->success($form);
    }

    public function updateSettings(Request $request, string $driver): JsonResponse
    {
        $class = resolve(DriverRepositoryInterface::class)
            ->getDriver('form-captcha', $driver, 'admin');

        $form = resolve($class);

        $parameters = [
            'driver' => $driver,
        ];

        $data = app()->call([$form, 'validated'], $parameters);

        $response = Settings::save($data);

        Artisan::call('cache:reset');

        $nextAction = [
            'type'    => 'navigate',
            'payload' => ['url' => '/admincp/captcha/type/browse', 'replace' => true],
        ];

        return $this->success($response, [
            'nextAction' => $nextAction,
        ], __p('core::phrase.save_changed_successfully'));
    }
}
