<?php

namespace MetaFox\Poll\Support\Form\Field;

use MetaFox\Form\AbstractField;

class AttachPoll extends AbstractField
{
    public const COMPONENT_NAME = 'AttachPoll';

    public function initialize(): void
    {
        $ownerId = request()->get('owner_id', null);

        $endpoint = url_utility()->makeApiUrl('poll/integration-form');

        if (is_numeric($ownerId) && $ownerId > 0) {
            $endpoint .= '?owner_id=' . $ownerId;
        }

        $this->name('integrated_item')
            ->component(self::COMPONENT_NAME)
            ->setAttribute('formUrl', $endpoint)
            ->fullWidth()
            ->placeholder(__p('poll::phrase.attach_poll'))
            ->variant('outlined');
    }
}
