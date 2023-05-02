<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionCancelReason\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Subscription\Models\SubscriptionPackage as Model;
use MetaFox\Subscription\Support\Helper;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchSubscriptionPackageForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class SearchForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/admincp/subscription-cancel-reason')
            ->submitAction('@formAdmin/search/SUBMIT')
            ->setValue([
                'statistic' => Helper::STATISTICS_ALL,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic([
            'sx' => [
                'flexFlow'   => 'wrap',
                'alignItems' => 'flex-start',
            ],
        ])->asHorizontal();

        $basic->addFields(
            Builder::choice('statistic')
                ->label(__p('subscription::admin.statistics_by_cancel_date'))
                ->options($this->getStatisticOptions())
                ->sizeSmall()
                ->marginDense()
                ->sxFieldWrapper([
                    'width' => MetaFoxForm::FIELD_WIDTH_ONE_THIRD,
                ])
                ->multiple(false)
                ->disableClearable(),
            Builder::date('statistic_from')
                ->startOfDay()
                ->returnKeyType('next')
                ->sizeSmall()
                ->label(__p('subscription::admin.from'))
                ->marginDense()
                ->sxFieldWrapper([
                    'width' => MetaFoxForm::FIELD_WIDTH_ONE_THIRD,
                ])
                ->showWhen([
                    'eq',
                    'statistic',
                    Helper::STATISTICS_CUSTOM,
                ])
                ->yup(
                    Yup::date()
                        ->when(
                            Yup::when('statistic')
                                ->is(Helper::STATISTICS_CUSTOM)
                                ->then(
                                    Yup::date()
                                        ->required()
                                        ->setError('typeError', __p('subscription::admin.you_must_choose_date_for_from'))
                                        ->setError('required', __p('subscription::admin.you_must_choose_date_for_from'))
                                )
                        )
                ),
            Builder::date('statistic_to')
                ->returnKeyType('next')
                ->endOfDay()
                ->label(__p('subscription::admin.to'))
                ->sizeSmall()
                ->marginDense()
                ->sxFieldWrapper([
                    'width' => MetaFoxForm::FIELD_WIDTH_ONE_THIRD,
                ])
                ->showWhen([
                    'eq',
                    'statistic',
                    Helper::STATISTICS_CUSTOM,
                ])
                ->yup(
                    Yup::date()
                        ->when(
                            Yup::when('statistic')
                                ->is(Helper::STATISTICS_CUSTOM)
                                ->then(
                                    Yup::date()
                                        ->required()
                                        ->min(['ref' => 'statistic_from'])
                                        ->setError('typeError', __p('subscription::admin.you_must_choose_date_for_to'))
                                        ->setError('required', __p('subscription::admin.you_must_choose_date_for_to'))
                                        ->setError('min', __p('subscription::admin.date_to_must_be_greater_than_or_equal_to_date_from'))
                                )
                        )
                ),
            Builder::submit()
                ->srOnly()
                ->label(__p('core::phrase.search'))
                ->marginDense(),
        );
    }

    protected function getStatisticOptions(): array
    {
        return Helper::getStatisticOptions();
    }
}
