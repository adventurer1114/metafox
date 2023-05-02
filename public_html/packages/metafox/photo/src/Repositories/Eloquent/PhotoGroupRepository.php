<?php

namespace MetaFox\Photo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Photo\Policies\PhotoGroupPolicy;
use MetaFox\Photo\Repositories\PhotoGroupRepositoryInterface;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class AlbumRepository.
 *
 * @mixin Builder
 * @property PhotoGroup $model
 * @method   PhotoGroup getModel()
 */
class PhotoGroupRepository extends AbstractRepository implements PhotoGroupRepositoryInterface
{
    public function model(): string
    {
        return PhotoGroup::class;
    }

    public function viewPhotoGroup(User $context, int $id): PhotoGroup
    {
        $photoSet = $this->with(['userEntity', 'ownerEntity', 'items'])->find($id);

        policy_authorize(PhotoGroupPolicy::class, 'view', $context, $photoSet);

        return $photoSet;
    }

    public function forceContentForGlobalSearch(array $files, ?string $content): array
    {
        if (count($files) != 1) {
            return $files;
        }

        foreach ($files as $key => $file) {
            $text = Arr::get($file, 'text');

            if (MetaFoxConstant::EMPTY_STRING == $text) {
                $text = null;
            }

            if (null !== $text) {
                continue;
            }

            if (null === $text) {
                $file['searchable_text'] = $content;
            }

            $files[$key] = $file;
        }

        return $files;
    }

    public function updateGlobalSearchForSingleMedia(PhotoGroup $group, ?string $text, int $total, ?array $oldFiles = null): void
    {
        $oldItems = $group->items;

        if (null === $oldItems) {
            return;
        }

        if ($total == 0) {
            return;
        }

        $checked = [];

        if (is_array($oldFiles)) {
            foreach ($oldFiles as $oldFile) {
                $checked[$oldFile['type']][] = $oldFile['id'];
            }
        }

        $first = $oldItems->first(function ($value) use ($checked) {
            if (!count($checked)) {
                return true;
            }

            $ids = Arr::get($checked, $value->itemType());

            if (null === $ids) {
                return true;
            }

            return !in_array($value->itemId(), $ids);
        });

        if (null === $first) {
            return;
        }

        $detail = $first->detail;

        if (!$detail instanceof HasGlobalSearch) {
            return;
        }

        if (null !== $detail->content) {
            return;
        }

        $searchable = $detail->toSearchable();

        if (null === $searchable) {
            return;
        }

        $content = null;

        if ($total == 1) {
            $content = $text;
        }

        $searchable = array_merge($searchable, [
            'text' => $content ?? MetaFoxConstant::EMPTY_STRING,
        ]);

        app('events')->dispatch('search.update_search_text', [$detail->entityType(), $detail->entityId(), $searchable], true);
    }

    public function handleContent(array $attributes, string $field = 'content'): array
    {
        if (Arr::has($attributes, $field)) {
            $content = Arr::get($attributes, $field);

            if (null == $content) {
                Arr::set($attributes, $field, MetaFoxConstant::EMPTY_STRING);
            }
        }

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function updateApprovedStatus(int $id): bool
    {
        $group = $this->getModel()->newModelQuery()
            ->with(['approvedItems'])
            ->find($id);

        if (null === $group) {
            return false;
        }

        $isApproved = $this->assertApproveStatus($group);
        $success    = $group->update(['is_approved' => $isApproved]);

        if ($group->isApproved() && $group->wasChanged('is_approved')) {
            app('events')->dispatch('models.notify.approved', [$group], true);
        }

        return $success;
    }

    private function assertApproveStatus(PhotoGroup $group): bool
    {
        $owner = $group->owner;
        if ($owner->hasPendingMode()) {
            return !$owner->isPendingMode();
        }

        return $group->approvedItems->count() > 0;
    }

    /**
     * @param  int  $id
     * @return bool
     */
    public function cleanUpGroup(int $id): bool
    {
        $group = $this->getModel()->newModelQuery()
            ->with(['items'])
            ->find($id);

        if (!$group instanceof PhotoGroup) {
            return true;
        }

        if (!$group->items()->count()) {
            $group->delete();

            return true;
        }

        return false;
    }

    public function deleteUserPhotoGroups(User $user): void
    {
        // Only delete photo groups which don't belongs to any album
        $groups = $this->getModel()
            ->newModelQuery()
            ->where('album_id', '=', 0)
            ->where(function (Builder $subQuery) use ($user) {
                $subQuery
                    ->where('owner_id', '=', $user->entityId())
                    ->orWhere('user_id', '=', $user->entityId());
            })
            ->get()
            ->collect();
        $ids   = $groups->pluck('id')->toArray();
        $query = PhotoGroupItem::query()->whereIn('group_id', $ids);

        foreach ($query->lazy() as $item) {
            if (!$item instanceof PhotoGroupItem) {
                continue;
            }

            $item->detail()->delete();
        }

        $groups->each->delete();
    }
}
