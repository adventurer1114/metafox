<?php

namespace MetaFox\Core\Http\Controllers\Api\v1;

use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use MetaFox\Core\Constants;
use MetaFox\Core\Http\Requests\v1\SiteSetting\Admin\StoreRequest;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Contracts\SiteSettingRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Core\Http\Controllers\Api\SiteSettingAdminController::$controllers.
 */

/**
 * Class SiteSettingAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @group admin/settings
 * @authenticated
 */
class SiteSettingAdminController extends ApiController
{
    /**
     * @var SiteSettingRepositoryInterface
     */
    private SiteSettingRepositoryInterface $repository;

    /**
     * SiteSettingAdminController constructor.
     *
     * @param  SiteSettingRepositoryInterface  $repository
     *
     * @ignore
     */
    public function __construct(SiteSettingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * View setting form.
     *
     * @param  string       $package
     * @param  string|null  $type
     *
     * @return JsonResponse
     */
    public function getSiteSettingForm(string $package, ?string $type = null): JsonResponse
    {
        $name = $type ? "$package.$type" : $package;

        if (!in_array($package, app('core.packages')->getActivePackageAliases())) {
            throw new RecordsNotFoundException();
        }

        $class = resolve(DriverRepositoryInterface::class)
            ->getDriver(Constants::DRIVER_TYPE_FORM_SETTINGS, $name, 'admin');

        $driver = new $class();

        return $this->success($driver);
    }

    /**
     * Update setting.
     *
     * @param  Request      $request
     * @param  string       $package
     * @param  string|null  $type
     * @return JsonResponse
     * @group admin/setting
     */
    public function store(Request $request, string $package, ?string $type = null): JsonResponse
    {
        $name = $type ? "$package.$type" : $package;

        $data = $request->all();

        $class = resolve(DriverRepositoryInterface::class)
            ->getDriver(Constants::DRIVER_TYPE_FORM_SETTINGS, $name, 'admin');

        $form = new $class();

        if (method_exists($form, 'boot')) {
            app()->call([$form, 'boot'], $request->route()->parameters());
        }

        if (method_exists($form, 'validated')) {
            $data = app()->call([$form, 'validated'], $request->route()->parameters());
        }

        $response = Settings::save($data);

        Artisan::call('cache:reset');

        app('events')->dispatch('site_settings.updated', $package);

        if (method_exists($form, 'redirectUrl')) {
            $url = $form->redirectUrl();

            if ($url) {
                $this->navigate($url);
            }
        }

        return $this->success($response, [], __p('core::phrase.save_changed_successfully'));
    }

    /**
     * @param  int  $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }
}
