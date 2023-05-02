<?php

namespace MetaFox\Group\Observers;

use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * Class GroupObserver.
 * @ignore
 */
class GroupObserver
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository)
    {
    }

    /**
     * @param Model $model
     */
    public function created(Model $model): void
    {
        $service = resolve(MemberRepositoryInterface::class);

        $service->updateGroupRole($model, $model->userId(), Member::ADMIN);

        $this->updateUserEntity($model);
        $category = $model->category;
        do {
            $category?->incrementTotalItem();
            $category = $category?->parentCategory;
        } while ($category);
    }

    public function updated(Model $model): void
    {
        $this->updateUserEntity($model);
        $this->updateCategoryCount($model);
    }

    public function deleted(Model $model): void
    {
        $model->groupText()->delete();

        $model->members()->delete();

        $model->groupRules()->delete();

        $category = $model->category;
        do {
            $category?->decrementTotalItem();
            $category = $category?->parentCategory;
        } while ($category);

        $requests = $model->requests;
        if (null !== $requests) {
            foreach ($requests as $request) {
                $request->delete();
            }
        }

        $invites = $model->invites;
        if (null !== $invites) {
            foreach ($invites as $invite) {
                $invite->delete();
            }
        }

        $questions = $model->groupQuestions;
        if (null !== $questions) {
            foreach ($questions as $question) {
                $question->delete();
            }
        }

        $changedPrivacies = $model->changedPrivacies;
        if ($changedPrivacies->count()) {
            foreach ($changedPrivacies as $changedPrivacy) {
                if ($changedPrivacy->delete()) {
                    app('events')->dispatch('notification.delete_mass_notification_by_item', [$changedPrivacy]);
                }
            }
        }
    }

    private function updateUserEntity(Model $model): void
    {
        $update = [
            'is_searchable' => PrivacyTypeHandler::PUBLIC == $model->privacy_type ? 1 : 0,
        ];

        if ($model->isDirty(['cover_file_id', 'cover_id'])) {
            $update = array_merge($update, [
                'avatar_file_id' => $model->cover_file_id,
                'avatar_id'      => $model->getAvatarId(),
                'avatar_type'    => $model->getAvatarType(),
            ]);
        }

        UserEntity::updateEntity($model->entityId(), $update);
    }

    private function updateCategoryCount(Model $model): void
    {
        if (!$model->isDirty('category_id')) {
            return;
        }

        $oldCategoryId = $model->getOriginal('category_id');
        if ($oldCategoryId) {
            $this->categoryRepository->find($oldCategoryId)?->decrementTotalItem();
        }

        $category = $model->category;
        do {
            $category?->incrementTotalItem();
            $category = $category?->parentCategory;
        } while ($category);
    }
}
