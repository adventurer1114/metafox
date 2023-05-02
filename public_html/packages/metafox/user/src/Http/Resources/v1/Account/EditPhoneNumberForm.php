<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\Account;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\User\Models\User;
use MetaFox\User\Policies\UserPolicy;
use MetaFox\Yup\Yup;

/**
 * Class EditPhoneNumberForm.
 * @property ?User $resource
 */
class EditPhoneNumberForm extends AbstractForm
{
    /**
     * @throws AuthenticationException
     */
    public function boot(): void
    {
        /** @var Model $context */
        $context        = user();
        $this->resource = $context;

        policy_authorize(UserPolicy::class, 'update', $context, $this->resource);
    }

    protected function prepare(): void
    {
        $this->asPut()
            ->action(url_utility()->makeApiUrl('/account/setting'))
            ->setValue([
                'phone_number' => $this->resource->profile->phone_number,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::phoneNumber('phone_number'),
        );

        $footer = $this->addFooter(['separator' => false]);

        $footer->addFields(
            Builder::submit()->label(__p('core::phrase.save'))->variant('contained'),
            Builder::cancelButton()->label(__p('core::phrase.cancel'))->variant('outlined'),
        );
    }
}
