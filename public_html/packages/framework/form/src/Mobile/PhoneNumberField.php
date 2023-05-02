<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Form\Constants;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

class PhoneNumberField extends TextField
{
    public function initialize(): void
    {
        $this->component(Constants::TEXT)
            ->variant('standardInlined')
            ->label(__p('core::phrase.phone_number'))
            ->placeholder(__p('core::phrase.phone_number'))
            ->yup(
                Yup::string()
                    ->matches(MetaFoxConstant::PHONE_NUMBER_REGEX, __p('validation.phone_number', [
                        'attribute' => __p('core::phrase.phone_number'),
                    ]))
            );
    }
}
