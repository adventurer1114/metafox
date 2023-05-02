<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\User\Models\CancelReason;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Repositories\CancelReasonRepositoryInterface;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 *
 * @driverType form-mobile
 * @driverName user.account.cancel
 * @property Model $resource
 */
class AccountCancelMobileForm extends AbstractForm
{
    public function boot(UserRepositoryInterface $repository, int $id = 0): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $this->title(__p('user::phrase.cancel_account'))
            ->action(apiUrl('user.account.cancel'))
            ->secondAction('@auth/logout')
            ->confirm([
                'title'   => __p('core::phrase.are_you_sure'),
                'message' => __p('core::phrase.action_cant_be_undone'),
            ])
            ->asPost();
    }

    public function initialize(): void
    {
        $this->addBasic()->addFields(
            Builder::choice('reason_id')
                ->label(__p('user::phrase.reason_for_leave'))
                ->options($this->getReasonOptions()),
            Builder::textArea('feedback')
                ->label(__p('user::phrase.please_explain'))
                ->asMultiLine(),
            Builder::password('password')
                ->label(__p('user::phrase.password'))
                ->required()
                ->yup(
                    Yup::string()->required()
                ),
        );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getReasonOptions(): array
    {
        $reasons = resolve(CancelReasonRepositoryInterface::class)->getReasonsForForm($this->resource);

        return $reasons->map(function (CancelReason $reason) {
            return [
                'label' => $reason->title,
                'value' => $reason->entityId(),
            ];
        })->values()->toArray();
    }
}
