<?php

namespace MetaFox\Core\Http\Controllers\Api\v1;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use MetaFox\App\Support\MetaFoxNews;
use MetaFox\Core\Http\Requests\v1\Dashboard\ChartDataRequest;
use MetaFox\Core\Http\Resources\v1\AdminAccess\AdminAccessItemCollection;
use MetaFox\Core\Http\Resources\v1\Statistic\ChartData;
use MetaFox\Core\Http\Resources\v1\Statistic\StatisticItemCollection;
use MetaFox\Core\Repositories\AdminAccessRepositoryInterface;
use MetaFox\Core\Repositories\StatsContentRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\MetaFox;
use MetaFox\Platform\MetaFoxConstant;

class DashboardAdminController extends ApiController
{
    private AdminAccessRepositoryInterface $accessRepository;

    private StatsContentRepositoryInterface $statRepository;

    public function __construct(
        AdminAccessRepositoryInterface $accessRepository,
        StatsContentRepositoryInterface $statRepository,
    ) {
        $this->accessRepository = $accessRepository;
        $this->statRepository   = $statRepository;
    }

    public function deepStatistic(): JsonResponse
    {
        $data = $this->statRepository->getDeepStatistic();

        return $this->success($data);
    }

    public function itemStatistic(): JsonResponse
    {
        $data = $this->statRepository->getItemStatistic();

        return $this->success(new StatisticItemCollection($data));
    }

    public function adminLogged(): JsonResource
    {
        $limit  = 2; //@todo: move this to a setting??!
        $result = $this->accessRepository->getLatestAccesses($limit);

        return new AdminAccessItemCollection($result);
    }

    /**
     * @throws AuthenticationException
     */
    public function activeAdmin(): JsonResource
    {
        $limit  = 3; //@todo: move this to a setting??!
        $result = $this->accessRepository->getActiveUsers(user(), $limit);

        return new AdminAccessItemCollection($result);
    }

    public function siteStatus(): JsonResponse
    {
        $expired = Settings::get('core.platform.expired_at');

        if (!$expired) {
            $expired =  null;
        }

        $latestVersion = Settings::get('core.platform.latest_version');

        return $this->success([
            'license_status'       => 'active',
            'license_status_style' => 'success',
            'installed_at'         => Settings::get('core.platform.installed_at'),
            'updated_at'           => Settings::get('core.platform.upgraded_at'),
            'license_expired_at'   => $expired,
            'version'              => MetaFox::getVersion(),
            'latest_version'       => $latestVersion,
            'can_upgrade'          => false, // temporarily disable platform upgrade
        ]);
    }

    public function metafoxNews(): JsonResponse
    {
        $result = (new MetaFoxNews())->getNews();

        return $this->success($result);
    }

    public function chartData(ChartDataRequest $request): JsonResponse
    {
        $params    = $request->validated();
        $data      = $this->statRepository->getChartData($params);
        $period    = Arr::get($params, 'period');
        $name      = Arr::get($params, 'name');
        $resources = new ChartData($data);
        $resources->setPeriod($period);
        $resources = $resources->toArray($request);

        // pick realtime or not ?
        /** @var array<int, array<string, mixed>> $rows */
        $rows  = $this->statRepository->getNowStats($period);
        $last  = collect($rows)->filter(function ($row) use ($name) {
            return $name === Arr::get($row, 'name');
        })->pop();

        if (!empty($last)) {
            array_pop($resources);
            $resources[] = [
                'data' => $last['value'],
                'date' => $this->statRepository->toDateFormatByPeriod($period, Carbon::now()),
            ];
        }

        return $this->success($resources);
    }

    public function statType(): JsonResponse
    {
        return $this->success($this->statRepository->getStatTypes(['online_user', 'pending_user']));
    }
}
