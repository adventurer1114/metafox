<?php

namespace MetaFox\Localize\Http\Resources\v1\Phrase\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Localize\Models\Phrase as Model;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\Facades\Settings;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchPhraseForm.
 * @property Model $resource
 */
class SearchPhraseForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit'))
            ->action('admincp/phrase')
            ->acceptPageParams(['q', 'locale', 'group', 'package_id'])
            ->setValue(['locale' => Settings::get('localize.default_locale', 'en')]);
    }

    protected function initialize(): void
    {
        $basic         = $this->addBasic(['variant' => 'horizontal']);
        $groupOptions  = resolve(PhraseRepositoryInterface::class)->getGroupOptions();

        $basic->addFields(
            Builder::text('q')
                ->forAdminSearchForm(),
            Builder::selectLocale('locale')
                ->forAdminSearchForm()
                ->disableClearable()
                ->label(__p('localize::phrase.language')),
            Builder::choice('group')
                ->forAdminSearchForm()
                ->label(__p('localize::phrase.group'))
                ->options($groupOptions),
            Builder::selectPackage('package_id')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.package_name')),
            Builder::submit()
                ->forAdminSearchForm()
        );
    }
}
