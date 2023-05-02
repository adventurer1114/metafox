<?php

namespace MetaFox\Page\Support;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Page\Contracts\PageContract;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

class PageSupport implements PageContract
{
    public const MENTION_REGEX = '^\[page=(.*?)\]^';

    public const SHARED_TYPE = 'page';

    /**
     * @var PageRepositoryInterface
     */
    protected $repository;

    /**
     * @param PageRepositoryInterface $repository
     */
    public function __construct(PageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMentions(string $content): array
    {
        $userIds = [];

        try {
            preg_match_all(self::MENTION_REGEX, $content, $matches);
            $userIds = array_unique($matches[1]);
        } catch (Exception $e) {
            // Silent.
        }

        return $userIds;
    }

    public function getPagesForMention(array $ids): Collection
    {
        $collection = $this->repository->getModel()->newModelQuery()
            ->whereIn('id', $ids)
            ->get();

        return $collection->mapWithKeys(function ($page) {
            return [$page->entityId() => $page];
        });
    }

    public function getPageBuilder(User $user): Builder
    {
        return $this->repository->getPageBuilder($user);
    }

    /**
     * getListTypes.
     *
     * @return array<mixed>
     */
    public function getListTypes(): array
    {
        return Cache::rememberForever('pages_list_types', function () {
            $resourceName = 'feed_type';

            $integrationTypes = $this->getDefaultListTypes($resourceName);

            $menuItems = resolve(MenuItemRepositoryInterface::class)
                ->getMenuItemByMenuName('page.page.profileMenu', 'web', true);

            if ($menuItems->count()) {
                foreach ($menuItems as $menuItem) {
                    if (is_string($menuItem->name)) {
                        $model = Relation::getMorphedModel($menuItem->name);

                        if (null !== $model) {
                            $model = resolve($model);
                        }

                        if ($model instanceof Content) {
                            $integrationTypes[] = [
                                'id'            => $model->entityType(),
                                'resource_name' => $resourceName,
                                'name'          => __p($menuItem->label),
                            ];
                        }
                    }
                }
            }

            return $integrationTypes;
        });
    }

    /**
     * getDefaultListTypes.
     *
     * @param  string       $resourceName
     * @return array<mixed>
     */
    protected function getDefaultListTypes(string $resourceName): array
    {
        if (!app_active('metafox/activity')) {
            return [];
        }

        $types[] = [
            'id'            => 'all',
            'resource_name' => $resourceName,
            'name'          => __p('core::phrase.all'),
        ];

        $postModel = Relation::getMorphedModel('activity_post');

        $linkModel = Relation::getMorphedModel('link');

        if (null !== $postModel) {
            $postModel = resolve($postModel);

            $types[] = [
                'id'            => $postModel->entityType(),
                'resource_name' => $resourceName,
                'name'          => __p('activity::phrase.posts'),
            ];
        }

        if (null !== $linkModel) {
            $linkModel = resolve($linkModel);

            $types[] = [
                'id'            => $linkModel->entityType(),
                'resource_name' => $resourceName,
                'name'          => __p('core::phrase.links'),
            ];
        }

        return $types;
    }

    public function isFollowing(User $context, User $user): bool
    {
        if (!app('events')->dispatch('follow.is_follow', [$context, $user], true)) {
            return false;
        }

        return true;
    }
}
