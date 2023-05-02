<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\ActivityPoint;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\ActivityPoint\Policies\StatisticPolicy;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
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
class GiftActivityPointForm extends AbstractForm
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
        $currentPoints = ActivityPoint::getTotalActivityPoints(user());
        $fullName      = $this->resource instanceof \MetaFox\User\Models\User ? $this->resource->full_name : '';

        $basic = $this->addBasic();
        $basic->addFields(
            Builder::typography('info')
                ->plainText(__p('activitypoint::phrase.gift_points_text', [
                    'current_points' => $currentPoints,
                    'user'           => $fullName,
                ]))
                ->color('text.secondary'),
            Builder::text('points')
                ->asNumber()
                ->preventScrolling()
                ->required()
                ->disabled($currentPoints <= 0)
                ->label(__p('activitypoint::phrase.how_many_points'))
                ->yup(
                    Yup::number()
                        ->required(__p('activitypoint::phrase.gifted_point_is_required'))
                        ->int()
                        ->min(1)
                        ->max($currentPoints, __p('activitypoint::phrase.you_have_only_points', ['points' => $currentPoints]))
                ),
            Builder::hidden('user_id'),
        );

        $this->addFooter()
            ->addFields(
                Builder::cancelButton(),
                Builder::submit()
                    ->label(__p('activitypoint::phrase.gift_points'))
                    ->disabled($currentPoints <= 0),
            );
    }
}
