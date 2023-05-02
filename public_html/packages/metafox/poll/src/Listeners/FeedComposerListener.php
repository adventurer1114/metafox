<?php

namespace MetaFox\Poll\Listeners;

use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\User;
use MetaFox\Poll\Models\Poll;
use MetaFox\Poll\Repositories\PollRepositoryInterface;

class FeedComposerListener
{
    /** @var PollRepositoryInterface */
    private PollRepositoryInterface $repository;

    public function __construct(PollRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  User                                           $user
     * @param  User                                           $owner
     * @param  string                                         $postType
     * @param  array                                          $params
     * @return array|int[]|null
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle(User $user, User $owner, string $postType, array $params): ?array
    {
        if ($postType != Poll::FEED_POST_TYPE) {
            return null;
        }

        if (false === app('events')->dispatch('activity.has_feature', [Poll::ENTITY_TYPE, 'can_create_feed'], true)) {
            return [
                'error_message' => __('validation.no_permission'),
            ];
        }

        $content = Arr::get($params, 'content', '');

        unset($params['content']);

        $pollParams = array_merge($params, [
            'text'    => '',
            'caption' => $content,
        ]);

        $poll = $this->repository->createPoll($user, $owner, $pollParams);

        $poll->load('activity_feed');

        return [
            'id' => $poll->activity_feed ? $poll->activity_feed->entityId() : 0,
        ];
    }
}
