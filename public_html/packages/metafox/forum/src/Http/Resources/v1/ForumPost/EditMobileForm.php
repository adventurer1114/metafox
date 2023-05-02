<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumPost;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Section;
use MetaFox\Forum\Models\ForumPost;
use MetaFox\Forum\Models\ForumPost as Model;
use MetaFox\Forum\Repositories\ForumPostRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * Class CreateMobileForm.
 * @property Model $resource
 */
class EditMobileForm extends AbstractForm
{
    public function boot(ForumPostRepositoryInterface $repository, ?int $id): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $resource = $this->resource;
        if (null !== $resource->postText) {
            $text = $resource->postText->text_parsed;
        }

        $values = [
            'text'        => $text,
            'attachments' => $resource->attachmentsForForm(),
        ];

        $this
            ->title(__('core::phrase.edit'))
            ->setBackProps(__p('forum::phrase.forums'))
            ->action('forum-post/' . $resource->entityId())
            ->asPut()
            ->setValue($values);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $this->addMoreBasic($basic);

        $basic->addFields(
            Builder::richTextEditor('text')
                ->required()
                ->returnKeyType('default')
                ->label(__p('forum::form.content'))
                ->placeholder(__p('forum::form.write_your_reply'))
                ->yup(
                    Yup::string()
                        ->required()
                        ->nullable(false),
                ),
            Builder::attachment()
                ->itemType(ForumPost::ENTITY_TYPE),
        );

        $this->addHidden($basic);
    }

    protected function addHidden(Section $basic): void
    {
        $basic->addFields(
            Builder::hidden('thread_id'),
        );
    }

    protected function addMoreBasic(Section $basic): void
    {
    }
}
