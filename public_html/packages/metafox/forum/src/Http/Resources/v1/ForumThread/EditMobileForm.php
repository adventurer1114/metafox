<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumThread;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Forum\Http\Requests\v1\ForumThread\CreateFormRequest;
use MetaFox\Forum\Policies\ForumThreadPolicy;
use MetaFox\Forum\Repositories\ForumThreadRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;

class EditMobileForm extends CreateMobileForm
{
    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function boot(CreateFormRequest $request, ForumThreadRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id ?? 0);
        $owner          = $context = user();
        $data           = $request->validated();

        if ($data['owner_id'] > 0 && $context->entityId() != $data['owner_id']) {
            $owner = UserEntity::getById($data['owner_id'])->detail;
        }

        if ($id !== null) {
            policy_authorize(ForumThreadPolicy::class, 'update', $context, $this->resource);
            $this->owner   = $owner;
            $this->ownerId = $owner->entityId();
        }
    }

    protected function prepare(): void
    {
        $resource = $this->resource;

        $text = null;

        $context = user();

        if (null !== $resource->description) {
            $text = $resource->description->text_parsed;
        }

        $tags = [];

        if (null !== $resource->tags) {
            $tags = $resource->tags;
        }

        $itemType = $resource->item_type;

        $itemId = $resource->item_id;

        $isSubscribed = null !== $resource->subscribed;

        $values = [
            'title'         => $resource->title,
            'text'          => $text,
            'is_closed'     => $resource->is_closed,
            'attachments'   => $resource->attachmentsForForm(),
            'tags'          => $tags,
            'item_type'     => $itemType,
            'item_id'       => $itemId,
            'is_subscribed' => (int) $isSubscribed,
            'is_wiki'       => (int) $resource->is_wiki,
            'id'            => $resource->entityId(),
        ];

        if ($resource->forum_id > 0) {
            Arr::set($values, 'forum_id', $resource->forum_id);
        }

        if (null !== $itemType) {
            $integratedItem = app('events')->dispatch(
                'forum.thread.integrated_item.edit_initialize',
                [$context, $itemType, $itemId, 'forum_thread.attach_poll'],
                true
            );

            if (is_array($integratedItem) && count($integratedItem) > 0) {
                $values['integrated_item'] = array_merge($integratedItem, [
                    'id' => $itemId,
                ]);
            }
        }

        $this
            ->title(__p('forum::phrase.edit_thread'))
            ->setBackProps(__p('forum::phrase.forums'))
            ->action('forum-thread/' . $resource->entityId())
            ->asPut()
            ->setValue($values);
    }
}
