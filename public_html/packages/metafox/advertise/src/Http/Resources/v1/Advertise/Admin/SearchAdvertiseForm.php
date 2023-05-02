<?php

namespace MetaFox\Advertise\Http\Resources\v1\Advertise\Admin;

use Illuminate\Support\Carbon;
use MetaFox\Advertise\Support\Facades\Support as Facade;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\MetaFoxConstant;

class SearchAdvertiseForm extends AbstractForm
{
    public function prepare(): void
    {
        $this->title('')
            ->asGet()
            ->action('admincp/advertise/advertise')
            ->acceptPageParams(['placement_id', 'start_date', 'end_date', 'title', 'full_name', 'status', 'is_active'])
            ->submitAction('@formAdmin/search/SUBMIT')
            ->setValue([
                'start_date' => null,
                'end_date'   => null,
            ]);
    }

    public function initialize(): void
    {
        $this->addBasic([
            'sx' => [
                'flexFlow'   => 'wrap',
                'alignItems' => 'flex-start',
            ],
        ])->asHorizontal()
            ->addFields(
                Builder::choice('placement_id')
                    ->label(__p('advertise::phrase.placement'))
                    ->options(Facade::getPlacementOptions(user(), false))
                    ->forAdminSearchForm(),
                Builder::date('start_date')
                    ->label(__p('advertise::phrase.start_date'))
                    ->startOfDay()
                    ->forAdminSearchForm(),
                Builder::date('end_date')
                    ->label(__p('advertise::phrase.end_date'))
                    ->endOfDay()
                    ->forAdminSearchForm(),
                Builder::text('title')
                    ->forAdminSearchForm()
                    ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH)
                    ->label(__p('core::phrase.title'))
                    ->placeholder(__p('core::phrase.title')),
                Builder::text('full_name')
                    ->forAdminSearchForm()
                    ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH)
                    ->placeholder(__p('advertise::phrase.creator'))
                    ->label(__p('advertise::phrase.creator')),
                Builder::choice('status')
                    ->label(__p('core::web.status'))
                    ->options($this->getStatusOptions())
                    ->forAdminSearchForm(),
                Builder::choice('is_active')
                    ->label(__p('core::phrase.is_active'))
                    ->options($this->getActiveOptions())
                    ->forAdminSearchForm(),
            );

        $this->addFooter([
            'sx' => [
                'flexFlow'   => 'wrap',
                'alignItems' => 'center',
            ],
        ])
            ->asHorizontal()
            ->addFields(
                Builder::submit()
                    ->label(__p('core::phrase.search')),
                Builder::clearSearchForm()
                    ->label(__p('core::phrase.reset'))
                    ->align('center'),
            );
    }

    protected function getActiveOptions(): array
    {
        return Facade::getActiveOptions();
    }

    protected function getStatusOptions(): array
    {
        return Facade::getAdvertiseStatusOptions();
    }
}
