<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointStatistic\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Models\PointStatistic as Model;
use MetaFox\ActivityPoint\Repositories\PointStatisticRepositoryInterface;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Contracts\User;
use MetaFox\Yup\Yup;

/**
 * Class AdjustPointStatisticForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName activitypoint_statistic.adjust
 * @driverType form
 */
class AdjustPointStatisticForm extends AbstractForm
{
    private string $type;
    private array $userIds;

    public function boot(Request $request, PointStatisticRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
        $params         = $request->all();
        $this->type     = (string) Arr::get($params, 'type', ActivityPoint::TYPE_SENT);
        $this->userIds  = [$id];
    }

    protected function prepare(): void
    {
        $this->title(__p('activitypoint::phrase.adjust_point'))
            ->action(apiUrl('admin.activitypoint.statistic.adjust'))
            ->asPut()
            ->setValue([
                'type'        => $this->type,
                'amount'      => '1',
                'target'      => $this->resource->userEntity?->name,
                'user_ids'    => $this->userIds,
                'mass_adjust' => false,
            ]);
    }

    protected function initialize(): void
    {
        $context = user();

        $maxPoint = (int) $context->getPermissionValue('activitypoint.maximum_activity_points_admin_can_adjust');

        $currentPoint = $this->resource->current_points;

        $basic = $this->addBasic();
        $basic->addFields(
            Builder::choice('type')
                ->options($this->getTypeOptions())
                ->label(__p('activitypoint::phrase.action')),
            Builder::text('target')
                ->disabled()
                ->label($this->getTargetLabelFromType($this->type)),
            $this->buildAmountField($context, $maxPoint, $currentPoint),
        );

        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->label(__p('activitypoint::phrase.adjust')),
                Builder::cancelButton(),
            );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getTypeOptions(): array
    {
        return [
            [
                'label' => __p('activitypoint::phrase.send_points'),
                'value' => ActivityPoint::TYPE_RECEIVED,
            ],
            [
                'label' => __p('activitypoint::phrase.reduce_points'),
                'value' => ActivityPoint::TYPE_SENT,
            ],
        ];
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
