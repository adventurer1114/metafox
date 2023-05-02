<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumThread;

use Illuminate\Support\Arr;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Platform\Contracts\Entity;

/**
 * Class EditForm.
 * @property ForumThread $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EditForm extends CreateForm
{
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
            'owner_id'      => $resource->owner_id,
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

    protected function setHiddenFieldById(Section $basic): void
    {
        $basic->addField(
            Builder::hidden('id'),
        );
    }

    protected function getItem(): ?Entity
    {
        return $this->resource->getItem();
    }
}
