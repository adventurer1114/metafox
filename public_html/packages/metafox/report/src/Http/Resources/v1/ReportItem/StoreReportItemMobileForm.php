<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItem;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Report\Http\Requests\v1\ReportItem\CreateFormRequest;
use MetaFox\Report\Models\ReportItem as Model;
use MetaFox\Report\Models\ReportReason;
use MetaFox\Report\Repositories\ReportReasonRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */
class StoreReportItemMobileForm extends AbstractForm
{
    /**
     * @var Model
     */
    public $resource;

    public function boot(CreateFormRequest $request): void
    {
        $params         = $request->validated();
        $this->resource = new Model($params);
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $termOfUserLink = 'https://preview-metafox.phpfox.us/backend/terms/';
        $reason         = Arr::first($this->getReportReason());

        $this->title(__p('report::phrase.report_title'))
            ->action(url_utility()->makeApiUrl('report'))
            ->asPost()
            ->description(__p('report::phrase.you_are_about_to_report_a_violation', ['link' => $termOfUserLink]))
            ->setValue([
                'item_id'   => $this->resource->item_id,
                'item_type' => $this->resource->item_type,
                'reason'    => Arr::get($reason, 'value'),
                'feedback'  => '',
            ]);
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::hidden('item_id')
                ->required(),
            Builder::hidden('item_type')
                ->required(),
            Builder::choice('reason')
                ->required()
                ->options($this->getReportReason())
                ->label(__p('user::phrase.reason'))
                ->placeholder(__p('core::phrase.select'))
                ->valueType('numeric')
                ->yup(
                    Yup::number()->required()
                ),
            Builder::richTextEditor('feedback')
                ->label(__p('report::phrase.a_comment_optional'))
                ->placeholder(__p('report::phrase.write_a_comment'))
        );
    }

    /**
     * @return array<int,              mixed>
     * @throws AuthenticationException
     */
    protected function getReportReason(): array
    {
        $reasonRepository = resolve(ReportReasonRepositoryInterface::class);
        $reasons          = $reasonRepository->getFormReason(user());

        return $reasons->map(function (ReportReason $reason) {
            return [
                'label' => $reason->name,
                'value' => $reason->entityId(),
            ];
        })->toArray();
    }
}
