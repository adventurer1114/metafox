<?php

namespace MetaFox\Core\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Core\Constants;
use MetaFox\Core\Http\Requests\v1\Core\CustomPrivacyOptionRequest;
use MetaFox\Core\Http\Requests\v1\Core\UrlToRouteRequest;
use MetaFox\Core\Http\Requests\v1\Link\FetchRequest;
use MetaFox\Core\Http\Resources\v1\Privacy\CustomPrivacyOptionCollection;
use MetaFox\Core\Http\Resources\v1\Privacy\CustomPrivacyOptionItem;
use MetaFox\Core\Repositories\Contracts\AppSettingRepositoryInterface;
use MetaFox\Core\Repositories\Contracts\PrivacyRepositoryInterface;
use MetaFox\Core\Repositories\Eloquent\DriverRepository;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Search\Repositories\SearchRepositoryInterface;
use StdClass;
use MetaFox\Friend\Http\Requests\v1\FriendList\StoreRequest as StoreFriendListRequest;

/**
 * Class CoreController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @group core
 */
class CoreController extends ApiController
{
    /**
     * @var AppSettingRepositoryInterface
     */
    private AppSettingRepositoryInterface $appSettingRepository;

    /**
     * @param AppSettingRepositoryInterface $appSettingRepository
     * @param SearchRepositoryInterface     $searchRepository
     */
    public function __construct(
        AppSettingRepositoryInterface $appSettingRepository,
        SearchRepositoryInterface $searchRepository
    ) {
        $this->appSettingRepository = $appSettingRepository;
        $this->searchRepository     = $searchRepository;
    }

    /**
     * @param  Request      $request
     * @param  string       $formName
     * @param  int|null     $id
     * @return JsonResponse
     * @link \MetaFox\Core\Http\Controllers\Api\v1\CoreAdminController::showForm()
     */
    public function showForm(Request $request, string $formName, $id = null)
    {
        if (!$formName) {
            return $this->error(__p('core::validation.could_not_find_form'));
        }

        /** @var AbstractForm $form */
        $driver = resolve(DriverRepository::class)
            ->getDriver(Constants::DRIVER_TYPE_FORM, $formName, 'web');

        $form = resolve($driver);

        if (method_exists($form, 'boot')) {
            app()->call([$form, 'boot'], $request->route()->parameters());
        }

        return $this->success($form->toArray($request));
    }

    /**
     * @param  Request      $request
     * @param  string       $formName
     * @param  int|null     $id
     * @return JsonResponse
     */
    public function showMobileForm(Request $request, string $formName, $id = null)
    {
        if (!$formName) {
            return $this->error(__p('core::validation.could_not_find_form'));
        }

        /** @var AbstractForm $form */
        $driver = app('core.drivers')->getDriver(Constants::DRIVER_TYPE_FORM, $formName, 'mobile');

        $form = resolve($driver);

        if (method_exists($form, 'boot')) {
            app()->call([$form, 'boot'], $request->route()->parameters());
        }

        return $this->success($form->toArray($request));
    }

    /**
     * @param  array  $arr
     * @return string
     */
    public function getLatestRevision(array $arr): string
    {
        $arr[] = Settings::versionId();

        return substr(md5(implode('-', array_map(function (mixed $value) {
            return sprintf('%s', $value);
        }, $arr))), -8);
    }

    /**
     * View app settings.
     *
     * @param  Request      $request
     * @param  string|null  $revision
     * @return JsonResponse
     * @group core
     */
    public function mobileSettings(Request $request, string $revision = null): JsonResponse
    {
        /** @var User $user */
        $user   = Auth::user();
        $locale = App::getLocale();
        $role   = resolve(RoleRepositoryInterface::class)->roleOf($user);

        $latestRevision = $this->getLatestRevision(['mobile', $locale, $role->id]);

        if ($latestRevision == $revision) {
            return $this->success(['keepCached' => 1]);
        }

        $settings = Cache::store('file')
            ->rememberForever($latestRevision, function () use ($request, $role) {
                return $this->appSettingRepository->getMobileSettings($request, $role);
            });

        $settings['revision'] = $latestRevision;

        return $this->success($settings);
    }

    /**
     * View frontend settings.
     *
     * @param  Request      $request
     * @param  string|null  $revision
     * @return JsonResponse
     * @group core
     */
    public function webSettings(Request $request, string $revision = null): JsonResponse
    {
        try{
            $locale = App::getLocale();
            /** @var User $user */
            $user = Auth::user();
            $role = resolve(RoleRepositoryInterface::class)->roleOf($user);

            $latestRevision = $this->getLatestRevision(['web', $locale, $role->id]);

            if ($latestRevision == $revision) {
                return $this->keepCacheSuccess();
            }

            $settings = Cache::store('file')
                ->rememberForever("settings.$latestRevision", function () use ($request, $role) {
                    return $this->appSettingRepository->getWebSettings($request, $role);
                });

            $settings['revision'] = $latestRevision;

            return $this->success($settings);
        }catch (\Exception $exception){
            Artisan::call('cache:reset');
            return $this->error($exception->getMessage());
        }
    }

    /**
     * View frontend settings.
     *
     * @param  Request      $request
     * @param  string|null  $revision
     * @return JsonResponse
     * @group core
     */
    public function adminSettings(Request $request, string $revision = null): JsonResponse
    {
        /** @var User $user */
        $user   = Auth::user();
        $locale = App::getLocale();
        $role   = resolve(RoleRepositoryInterface::class)->roleOf($user);

        $latestRevision = $this->getLatestRevision(['mobile', $locale, $role->id]);

        if ($latestRevision == $revision) {
            return $this->keepCacheSuccess();
        }

        $settings = Cache::store('file')
            ->rememberForever("settings.$latestRevision", function () use ($request, $role) {
                return $this->appSettingRepository->getAdminSettings($request, $role);
            });

        $settings['revision'] = $latestRevision;

        return $this->success($settings);
    }

    /**
     * Get canonical URL.
     *
     * @param UrlToRouteRequest $request
     *
     * @return JsonResponse
     * @group core
     */
    public function urlToRoute(UrlToRouteRequest $request): JsonResponse
    {
        $params = $request->validated();

        $url = $params['url'];

        $result = app('events')->dispatch('parseRoute', [$url], true);

        if ($result) {
            return $this->success($result);
        }

        return $this->error('route not found.');
    }

    /**
     * View user badge status.
     *
     * @throws AuthenticationException
     * @group core
     */
    public function status(): JsonResponse
    {
        $user = user();
        $data = new StdClass();

        app('events')
            ->dispatch('core.badge_counter', [$user, $data]);

        return $this->success($data);
    }

    /**
     * View link.
     *
     * @param FetchRequest $request
     *
     * @return JsonResponse
     * @group core
     */
    public function fetchLink(FetchRequest $request): JsonResponse
    {
        $params = $request->validated();

        $url = $params['link'];

        $data = app('events')
            ->dispatch('core.parse_url', [$url], true);

        if (empty($data)) {
            return $this->error(__p('core::phrase.invalid_link'));
        }

        return $this->success($data);
    }

    /**
     * @param  string       $group
     * @param  string|null  $locale
     * @param  string|null  $revision
     * @return JsonResponse
     */
    public function loadTranslation(string $group, string $locale = null, string $revision = null): JsonResponse
    {
        $locale = $locale == 'auto'?  App::getLocale(): $locale;

        $latestRevision = $this->getLatestRevision(['web', $group, $locale]);

        if ($revision === $latestRevision) {
            return $this->keepCacheSuccess([
                'revision'=> $latestRevision,
                '$locale' => $locale,
            ]);
        }

        $data = app('translation.loader')->load($locale, $group, null);

        $data['revision'] = $latestRevision;
        $data['$locale'] = $locale;

        return $this->success($data);
    }

    public function getCustomPrivacyOptions(CustomPrivacyOptionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $context = user();

        $lists = resolve(PrivacyRepositoryInterface::class)->getCustomPrivacyOptions($context, $data);

        return $this->success(new CustomPrivacyOptionCollection($lists));
    }

    public function checkInstalled()
    {
        return $this->error('This site is installed');
    }

    public function createCustomPrivacyOption(StoreFriendListRequest $request): JsonResponse
    {
        $params     = $request->validated();

        $context = user();

        $friendList = app('events')->dispatch('friend.friend_list.create', [$context, $params], true);

        if (null === $friendList) {
            return $this->error('', 403);
        }

        $friendList->is_selected = true;

        return $this->success(new CustomPrivacyOptionItem($friendList), [], __p(
            'core::phrase.resource_create_success',
            ['resource_name' => __p('friend::phrase.friend_list')]
        ));
    }
}
