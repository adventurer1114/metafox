<?php

namespace MetaFox\Event\Http\Resources\v1\Event;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Event\Models\Event as Model;
use MetaFox\Event\Policies\EventPolicy;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\Privacy;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditEventForm.
 * @property Model $resource
 */
class EditEventForm extends CreateEventForm
{
    /** @var bool */
    protected $isEdit = true;

    protected function prepare(): void
    {
        $eventText = $this->eventText?->text_parsed;

        $privacy = $this->resource->privacy;

        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $this->title(__p('event::phrase.edit_event'))
            ->setBackProps(__p('core::phrase.events'))
            ->action(url_utility()->makeApiUrl('/event/' . $this->resource->entityId()))
            ->asPut()
            ->setValue([
                'name'        => $this->resource->name ?? '',
                'text'        => $eventText ?? '',
                'privacy'     => $privacy,
                'owner_id'    => $this->resource->owner_id,
                'attachments' => $this->resource->attachmentsForForm(),
                'categories'  => $this->resource->categories->pluck('id')->toArray(),
                'is_online'   => $this->resource->is_online,
                'event_url'   => $this->resource->event_url,
                'start_time'  => $this->resource->start_time,
                'end_time'    => $this->resource->end_time,
                'host'        => $this->memberRepository()->getEventHostsForForm($this->resource),
                'location'    => $this->resource->toLocationObject(),
            ]);
    }

    /**
     * @throws AuthenticationException
     */
    protected function buildPrivacyField(): Privacy
    {
        $context = user();

        return Builder::privacy('privacy')
            ->description(__p('event::phrase.control_who_can_see_this_event'))
            ->disabled($this->isDisableEventFields())
            ->showWhen([
                'or',
                [
                    'falsy',
                    'owner_id',
                ], [
                    'eq',
                    'owner_id',
                    $context->entityId(),
                ],
            ]);
    }

    protected function isDisableEventFields(): bool
    {
        return $this->resource instanceof Model && $this->resource->isEnded();
    }

    protected function canManageHosts(): bool
    {
        $context = user();

        return policy_check(EventPolicy::class, 'manageHosts', $context, $this->resource);
    }
}
