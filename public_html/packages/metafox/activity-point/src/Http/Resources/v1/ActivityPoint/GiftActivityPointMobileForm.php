<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\ActivityPoint;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\ActivityPoint\Policies\StatisticPolicy;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\Yup\Yup;

/**
 * Class GiftActivityPointForm.
 * @property User $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverType form
 * @driverName activitypoint.gift
 */
class GiftActivityPointMobileForm extends AbstractForm
{
    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function boot(int $id): void
    {
        $this->resource = UserEntity::getById($id)->detail;

        policy_authorize(StatisticPolicy::class, 'giftPoint', user(), $this->resource);
    }

    protected function prepare(): void
    {
        $this->title(__p('activitypoint::phrase.gift_points'))
            ->action(apiUrl('activitypoint.gift', ['id' => $this->resource->entityId()]))
            ->asPost()
            ->setValue([
                'user_id' => $this->resource->entityId(),
            ]);
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $context         = user();
        $currentPoints   = ActivityPoint::getTotalActivityPoints($context);
        $fullName        = $this->resource instanceof \MetaFox\User\Models\User ? $this->resource->full_name : '';
        $basic           = $this->addBasic();
        $isDisabledPoint = false;
        $yup             = Yup::number()
            ->required(__p('activitypoint::phrase.gifted_point_is_a_required_field'))
            ->int()
            ->min(1);

        $info = __p('activitypoint::phrase.gift_points_text', [
            'current_points' => $currentPoints,
            'user'           => $fullName,
        ]);

        if (!$context->hasSuperAdminRole()) {
            $isDisabledPoint = $currentPoints <= 0;
            $info            = __p('activitypoint::phrase.supper_admin_gift_points_text', ['user' => $fullName]);

            $yup->max($currentPoints, __p('activitypoint::phrase.you_have_only_points', ['points' => $currentPoints]));
        }

        $basic->addFields(
            Builder::typography('info')
                ->plainText($info),
            Builder::text('points')
                ->asNumber()
                ->required()
                ->disabled($isDisabledPoint)
                ->label(__p('activitypoint::phrase.how_many_points'))
                ->yup($yup),
            Builder::hidden('user_id'),
        );
    }
}
