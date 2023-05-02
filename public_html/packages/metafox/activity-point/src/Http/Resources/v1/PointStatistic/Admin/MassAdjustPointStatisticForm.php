<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointStatistic\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Models\PointStatistic as Model;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint as ActivityPointFacade;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\Yup\Yup;

/**
 * Class MassAdjustPointStatisticForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName activitypoint_statistic.mass_adjust
 * @driverType form
 */
class MassAdjustPointStatisticForm extends AbstractForm
{
    private string $type;
    private array $name;
    private array $userIds;

    public function boot(Request $request): void
    {
        $this->userIds = json_decode($request->get('user_ids', []));
        $this->type    = $request->get('type', ActivityPoint::TYPE_SENT);
        $this->name    = [];
        if (!empty($this->userIds)) {
            $user       = UserEntity::getByIds($this->userIds);
            $this->name = $user->pluck('name')->toArray();
        }
    }

    protected function prepare(): void
    {
        $this->title(__p('activitypoint::phrase.adjust_point'))
            ->action(apiUrl('admin.activitypoint.statistic.adjust'))
            ->asPut()
            ->setValue([
                'type'        => $this->type,
                'amount'      => '1',
                'target'      => $this->name,
                'user_ids'    => $this->userIds,
                'mass_adjust' => true,
            ]);
    }

    protected function initialize(): void
    {
        $context = user();

        $maxPoint = (int) $context->getPermissionValue('activitypoint.maximum_activity_points_admin_can_adjust');

        $currentPoint = ActivityPointFacade::getMinPointByIds($this->userIds);

        $basic = $this->addBasic();
        $basic->addFields(
            Builder::choice('type')
                ->options($this->getTypeOptions($currentPoint))
                ->label(__p('activitypoint::phrase.action')),
            Builder::tags('target')
                ->disabled()
                ->multiple(true)
                ->label($this->getTargetLabelFromType($this->type)),
            $this->buildAmountField($context, $maxPoint, $currentPoint),
        );

        $this->addFooter()
            ->addFields(
                Builder::cancelButton(),
                Builder::submit()
                    ->label(__p('core::phrase.submit')),
            );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getTypeOptions(int $currentPoint): array
    {
        $data = [
            [
                'label' => __p('activitypoint::phrase.send_points'),
                'value' => ActivityPoint::TYPE_RECEIVED,
            ],
        ];

        if ($currentPoint) {
            $data[] = [
                'label' => __p('activitypoint::phrase.reduce_points'),
                'value' => ActivityPoint::TYPE_SENT,
            ];
        }

        return $data;
    }

    protected function getTargetLabelFromType(string $type): string
    {
        $labels = [
            ActivityPoint::TYPE_SENT     => __p('activitypoint::phrase.reduce_from'),
            ActivityPoint::TYPE_RECEIVED => __p('activitypoint::phrase.sent_to'),
        ];

        return Arr::get($labels, $type, '');
    }

    protected function buildAmountField(User $context, int $maxPoint, int $currentPoint): AbstractField
    {
        if ($context->hasSuperAdminRole()) {
            return Builder::text('amount')
                ->required()
                ->label(__p('activitypoint::phrase.points'))
                ->yup(
                    Yup::number()
                    ->int()
                    ->min(1)
                    ->when(
                        Yup::when('type')
                        ->is(ActivityPoint::TYPE_SENT)
                        ->then(
                            Yup::number()
                            ->int()
                            ->min(1)
                        )
                    )
                );
        }

        return Builder::text('amount')
            ->required()
            ->label(__p('activitypoint::phrase.points'))
            ->yup(
                Yup::number()
                    ->int()
                    ->min(1)
                    ->max($maxPoint)
                    ->setError('max', __p('activitypoint::validation.maximum_points_for_sending'))
                    ->when(
                        Yup::when('type')
                            ->is(ActivityPoint::TYPE_SENT)
                            ->then(
                                Yup::number()
                                    ->int()
                                    ->min(1)
                                    ->max($currentPoint)
                                    ->setError('max', __p('activitypoint::validation.maximum_points_for_reducing'))
                            ),
                    ),
            )
            ->warning(__p(
                'activitypoint::phrase.maximum_points_for_sending',
                ['point' => $maxPoint]
            ) . __p(
                'activitypoint::phrase.maximum_points_for_reducing',
                ['point' => $currentPoint]
            ));
    }
}
