<?php

namespace MetaFox\Mobile\Repositories\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Mobile\Repositories\AdMobPageAdminRepositoryInterface;
use MetaFox\Mobile\Models\AdMobPage as Model;
use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Support\Browse\Scopes\User\RoleScope;

/**
 * Class AdMobPageAdminRepository.
 */
class AdMobPageAdminRepository extends AbstractRepository implements AdMobPageAdminRepositoryInterface
{
    public function model()
    {
        return Model::class;
    }

    /**
     * @inheritDoc
     */
    public function getConfigForSettings(User $user): Collection
    {
        $userRole  = $user->roles->first();
        $roleScope = new RoleScope();
        $roleScope->setRole((string) $userRole->entityId());

        return $this->getModel()
            ->newModelQuery()
            ->has('configs')
            ->with('configs', function (Builder $subQuery) use ($roleScope) {
                $subQuery->where('is_active', '=', 1);
                $subQuery->addScope($roleScope);
            })
            ->get()
            ->collect();
    }

    /**
     * @inheritDoc
     */
    public function getPageOptions(): array
    {
        return $this->getModel()
            ->newModelQuery()
            ->get()
            ->collect()
            ->map(function (Model $page) {
                return [
                    'label' => $page->name,
                    'value' => $page->entityId(),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * @param int    $pageid
     * @param string $configType
     */
    public function canAddConfigToPage(int $pageId, string $configType): bool
    {
        $page = $this->getModel()->newModelQuery()->find($pageId);
        if (!$page instanceof Model) {
            return false;
        }

        return $page->configs()->wherePivot('config_type', '=', $configType)->doesntExist();
    }
}
