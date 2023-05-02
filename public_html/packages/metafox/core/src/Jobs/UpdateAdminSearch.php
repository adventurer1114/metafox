<?php

namespace MetaFox\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use MetaFox\App\Models\Package;
use MetaFox\Core\Repositories\AdminSearchRepositoryInterface;
use MetaFox\Form\Builder;
use MetaFox\Menu\Models\MenuItem;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * stub: packages/jobs/job-queued.stub.
 * @link \MetaFox\Core\Jobs\UpdateAdminSearch::dispatchSync()
 */
class UpdateAdminSearch implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ?string $packageName;

    private AdminSearchRepositoryInterface $searchRepository;
    /**
     * @var MenuItemRepositoryInterface
     */
    private MenuItemRepositoryInterface $menuItemRepository;

    private string $likeOperator;

    /**
     * @param ?string $packageName
     */
    public function __construct(?string $packageName = null)
    {
        $this->packageName = $packageName;
    }

    public function uniqueId(): string
    {
        return __CLASS__;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Builder::refresh();
        $this->searchRepository   = resolve(AdminSearchRepositoryInterface::class);
        $this->menuItemRepository = resolve(MenuItemRepositoryInterface::class);
        $this->likeOperator       = $this->menuItemRepository->likeOperator();

        // force auth superuser.

        Auth::setUser(resolve(UserRepositoryInterface::class)->find(1));
        $this->searchRepository->clean();
        $this->scanMenuItems();
        $this->scanApps();
    }

    public function scanMenuItems()
    {
        /** @var Collection<MenuItem> $parents */
        $parents = $this->menuItemRepository->getModel()
            ->newQuery()
            ->where([
                ['menu', '=', 'core.adminSidebarMenu'],
                ['as', '=', 'subMenu'],
                ['is_active', '=', 1],
            ])->get();

        foreach ($parents as $parent) {
            if (!$parent->label) {
                continue;
            }

            $this->searchRepository
                ->fromMenuItems([
                    ['menu', '=', 'core.adminSidebarMenu'],
                    ['parent_name', '=', $parent->name],
                    ['is_active', '=', 1],
                ], __p($parent->label), __p($parent->label));
        }
    }

    private function scanApps()
    {

        /** @var Collection<Package> $apps */
        $apps = resolve('core.packages')
            ->getModel()
            ->newQuery()
            ->where([['is_active', '=', 1]])->get();

        foreach ($apps as $app) {
            $this->searchRepository->scanApp($app);
        }
    }
}
