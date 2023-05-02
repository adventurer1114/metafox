<?php

namespace MetaFox\App\Http\Resources\v1\AppStoreProduct\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Localize\Models\Phrase as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchForm.
 * @property Model $resource
 */
class SearchForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->noHeader(true)
            ->asGet()
            ->title(__p('core::phrase.search'))
            ->action('admincp/app/store/product')
            ->acceptPageParams(['q', 'type', 'category', 'price_filter', 'sort', 'featured'])
            ->submitAction('@formAdmin/search/SUBMIT');
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic(['variant' => 'horizontal']);

        $basic->addFields(
            Builder::text('q')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.search_dot')),
            Builder::choice('type')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.type'))
                ->placeholder(__p('core::phrase.type'))
                ->options($this->getAllowedOptionsForForm('type')),
            Builder::choice('category')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.category'))
                ->placeholder(__p('core::phrase.all_category'))
                ->options($this->getAllowedOptionsForForm('category')),
            Builder::choice('price_filter')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.price'))
                ->placeholder(__p('core::phrase.all_price'))
                ->options($this->getAllowedOptionsForForm('price_filter')),
            Builder::choice('sort')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.sort_label'))
                ->placeholder(__p('core::phrase.sort_by'))
                ->options($this->getAllowedOptionsForForm('sort')),
            Builder::choice('featured')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.featured'))
                ->placeholder(__p('core::phrase.all'))
                ->options($this->getAllowedOptionsForForm('featured')),
            Builder::submit()
                ->forAdminSearchForm()
                ->label(__p('core::phrase.search')),
        );
    }

    /**
     * getAllowedTypes.
     *
     * @param  string        $key
     * @return array<string>
     */
    public static function getAllowedOptions(string $key): array
    {
        $availableOptions = [
            'type'         => ['app', 'theme', 'language'],
            'category'     => ['social_publishing', 'spam_security'],
            'price_filter' => ['free', 'paid'],
            'sort'         => ['recent_updated', 'latest', 'top_rated'],
            'featured'     => ['yes', 'no'],
        ];

        return Arr::get($availableOptions, $key, []);
    }

    /**
     * getAllowedTypesForForm.
     *
     * @param  string                     $key
     * @return array<array<string,mixed>>
     */
    public function getAllowedOptionsForForm(string $key): array
    {
        $options = [
            [
                'label' => __p('core::phrase.all'),
                'value' => '',
            ],
        ];

        foreach (self::getAllowedOptions($key) as $option) {
            array_push($options, [
                'label' => __p("core::store.$key.$option"),
                'value' => $option,
            ]);
        }

        return $options;
    }
}
