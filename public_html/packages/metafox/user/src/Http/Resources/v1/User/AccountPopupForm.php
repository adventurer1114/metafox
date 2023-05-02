<?php

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Form\Section;
use MetaFox\User\Models\User as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class AccountPopupForm.
 * @property ?Model $resource
 * @preload 1
 */
class AccountPopupForm extends LoginPopupUserForm
{
    protected function initialize(): void
    {
        $header = $this->addSection(
            Builder::section('header')
        );

        $header->addFields(
            Builder::typography('form_header')
                ->plainText(__p('user::web.add_existing_account'))
                ->variant('h3')
                ->sx([
                    'justifyContent' => 'center',
                    'display'        => 'flex',
                ])
        );

        parent::initialize();

        $bottom = $this->addSection(
            Builder::section('bottom')
                ->variant('horizontal')
        );

        $this->handleSocialLoginFields($bottom);
    }

    /**
     * @param Section $section
     *
     * @return void
     */
    protected function handleSocialLoginFields(Section $section): void
    {
        $fieldResponses = array_filter(app('events')->dispatch('socialite.login_fields', ['web']) ?? []);
        if (empty($fieldResponses)) {
            return;
        }

        $section->addField(Builder::typography('social_login')
            ->setAttribute('class', 'typoSigInSocialite')
            ->plainText(__p('user::phrase.or_sign_in_using')));

        foreach ($fieldResponses as $response) {
            $section->addFields(...$response);
        }
    }
}
