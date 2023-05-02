<?php

namespace MetaFox\Platform\Support\Factory;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;

/**
 * Trait HasSetState.
 * @mixin Factory
 */
trait HasSetState
{
    /**
     * @param User $user
     *
     * @return $this
     */
    public function forUser(User $user): static
    {
        return $this->state(function () use ($user) {
            return [
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
            ];
        });
    }

    /**
     * @param User $owner
     *
     * @return $this
     */
    public function forOwner(User $owner): static
    {
        return $this->state(function () use ($owner) {
            return [
                'owner_id'   => $owner->entityId(),
                'owner_type' => $owner->entityType(),
            ];
        });
    }

    /**
     * @param Entity $item
     *
     * @return $this
     */
    public function forItem(Entity $item): static
    {
        return $this->state(function () use ($item) {
            return [
                'item_id'   => $item->entityId(),
                'item_type' => $item->entityType(),
            ];
        });
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user): static
    {
        return $this->state(function () use ($user) {
            return [
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
            ];
        });
    }

    /**
     * @param User      $user
     * @param User|null $owner
     *
     * @return $this
     */
    public function setUserAndOwner(User $user, User $owner = null): static
    {
        if (!$owner) {
            $owner = $user;
        }

        return $this->state(function (array $attributes) use ($user, $owner) {
            $result = [];
            if (array_key_exists('user_id', $attributes)) {
                $result['user_id'] = $user->entityId();
            }

            if (array_key_exists('user_type', $attributes)) {
                $result['user_type'] = $user->entityType();
            }

            if (array_key_exists('owner_id', $attributes)) {
                $result['owner_id'] = $owner->entityId();
            }

            if (array_key_exists('owner_type', $attributes)) {
                $result['owner_type'] = $owner->entityType();
            }

            return $result;
        });
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setOwner(User $user)
    {
        return $this->state(function () use ($user) {
            return [
                'owner_id'   => $user->entityId(),
                'owner_type' => $user->entityType(),
            ];
        });
    }

    /**
     * @param  array $list
     * @return $this
     */
    public function setCustomPrivacy(array $list = []): static
    {
        if (!empty($list)) {
            return $this->state(function () use ($list) {
                return [
                    'privacy'      => MetaFoxPrivacy::CUSTOM,
                    'privacy_list' => $list,
                ];
            });
        }

        return $this;
    }

    /**
     * @param Content $item
     *
     * @return static
     */
    public function setItem(Content $item): static
    {
        return $this->state(function () use ($item) {
            return [
                'item_id'   => $item->entityId(),
                'item_type' => $item->entityType(),
            ];
        });
    }

    /**
     * @param array $categories
     *
     * @return static
     */
    public function setCategories(array $categories = []): static
    {
        if (empty($categories)) {
            $numberCategory = mt_rand(1, 3);
            $categories     = [];
            for ($i = 0; $i < $numberCategory; $i++) {
                $categories[] = mt_rand(1, 4);
            }
        }

        return $this->state(function () use ($categories) {
            return ['categories' => $categories];
        });
    }

    /**
     * @return $this
     */
    public function setRandomPrivacy(): static
    {
        return $this->state(function ($attributes) {
            if (!array_key_exists('privacy', $attributes)
                || !array_key_exists('owner_type', $attributes)
                || !array_key_exists('owner_id', $attributes)) {
                return [];
            }

            $privacy = random_privacy($attributes['owner_type'] ?? 'user');

            if ($privacy === 4) {
                $list = DB::table('friend_lists')->where('user_id', '=', $attributes['owner_id'] ?? 1)
                    ->inRandomOrder()
                    ->limit(mt_rand(2, 5))->pluck('id')->toArray();

                return ['privacy_list' => $list, 'privacy' => 4];
            }

            return [
                'privacy' => $privacy,
            ];
        });
    }

    /**
     * @param $userId
     * @param $limit
     * @return mixed
     * @deprecated
     */
    public function fakeRandomId($userId, $limit)
    {
        $data = $this->getModel()->newQuery()
            ->where(['user_id' => $userId])
            ->pluck('id');

        return $data->count() > $limit ? $data->random($limit)->toArray() : $data->toArray();
    }

    public function pickRandomContent(): ?Content
    {
        $types = [
            'feed',
            'activity_post',
            'announcement',
            'blog',
            'comment',
            'event',
            'feed',
            'forum_post',
            'forum_thread',
            'group',
            'link',
            'marketplace',
            //            'music_album',
            //            'music_playlist',
            //            'music_song',
            'page',
            'photo',
            'photo_album',
            'photo_set',
            'poll',
            'quiz',
            'share',
            'user',
            'video',
        ];
        $type  = $this->faker->randomElement(['blog']);
        $model = Relation::getMorphedModel($type);

        return $model::query()->inRandomOrder()->first();
    }

    public function pickOtherUserId(
        int $id,
        string $otherModelName,
        string $column1 = 'owner_id',
        string $column2 = 'user_id'
    ) {
        $id = \MetaFox\User\Models\User::query()
            ->select(['id'])
            ->whereNotIn('id', $otherModelName::query()->select($column1)->where($column2, $id))
            ->where('id', '<>', $id)
            ->inRandomOrder()
            ->value('id');

        return $id ? $id : 1;
    }

    /**
     * @return $this
     */
    public function seed()
    {
        return $this;
    }
}
