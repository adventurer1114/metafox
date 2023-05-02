<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Event\Http\Resources\v1\Event;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Event\Models\Event as Model;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Event\Support\Facades\Event;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Yup\Yup;

/**
 * @property Model $resource
 * @preload 1
 */
class MassEmailForm extends AbstractForm
{
    public function boot(
        EventRepositoryInterface $repository,
        ?int $id = null,
    ): void {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $eventId = $this->resource?->id;
        $this->title(__p('event::phrase.mass_email_guest'))
            ->action("/event/{$eventId}/mass-email")
            ->asPost()
            ->acceptPageParams(['subject', 'text']);
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $isDisabled = $this->isDisableSubmitField();
        $basic->addFields(
            Builder::text('subject')->required()
                ->label(__p('event::phrase.subject'))
                ->placeholder(__p('event::phrase.subject'))
                ->yup(Yup::string()
                    ->required()),
            Builder::textArea('text')
                ->label(__p('event::phrase.text'))
                ->placeholder(__p('event::phrase.text')),
        );
        $this->addFooter()
            ->addFields(
                Builder::submit()->label(__p('event::phrase.send'))
                    ->disabled($isDisabled),
            );
    }

    /**
     * @throws AuthenticationException
     */
    protected function isDisableSubmitField(): bool
    {
        return Event::checkPermissionMassEmail(user(), $this->resource->entityId());
    }
}
