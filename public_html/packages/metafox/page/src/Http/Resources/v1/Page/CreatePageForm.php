<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\CancelButton;
use MetaFox\Page\Models\Category;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreatePageForm.
 * @property Model $resource
 */
class CreatePageForm extends AbstractForm
{
    public const MAX_TITLE_LENGTH = 64;
    public const MAX_TEXT_LENGTH  = 3000;
    /** @var bool */
    protected $isEdit = false;

    protected function prepare(): void
    {
        $categoryRepository = resolve(PageCategoryRepositoryInterface::class);
        $categoryDefault    = $categoryRepository->getCategoryDefault();
        $values             = [];

        if ($categoryDefault?->is_active == Category::IS_ACTIVE) {
            $values = ['category_id' => $categoryDefault->entityId()];
        }

        $this->title(__('page::phrase.create_new_page'))
            ->asPost()
            ->setBackProps(__p('core::web.pages'))
            ->action(url_utility()->makeApiUrl('page'))
            ->setValue($values);
    }

    protected function initialize(): void
    {
        $variant           = 'outlined';
        $minPageNameLength = Settings::get('page.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxPageNameLength = Settings::get('page.maximum_name_length', self::MAX_TITLE_LENGTH);

        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('name')
                ->required()
                ->variant($variant)
                ->minLength($minPageNameLength)
                ->maxLength($maxPageNameLength)
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxPageNameLength]))
                ->label(__p('core::phrase.title'))
                ->placeholder(__p('page::phrase.fill_in_a_name_for_your_page'))
                ->yup(
                    Yup::string()
                        ->required()
                        ->minLength(
                            $minPageNameLength,
                            __p(
                                'core::validation.title_minimum_length_of_characters',
                                ['number' => $minPageNameLength]
                            )
                        )
                        ->maxLength(
                            $maxPageNameLength,
                            __p('core::validation.title_maximum_length_of_characters', [
                                'min' => $minPageNameLength,
                                'max' => $maxPageNameLength,
                            ])
                        )
                ),
            Builder::textArea('text')
                ->variant($variant)
                ->maxLength(self::MAX_TEXT_LENGTH)
                ->label(__p('core::phrase.description')),
            Builder::category('category_id')
                ->variant($variant)
                ->required()
                ->label(__p('core::phrase.category'))
                ->multiple(false)
                ->valueType('number')
                ->setRepository(PageCategoryRepositoryInterface::class)
                ->yup(Yup::number()->required()),
        );

        if (app_active('metafox/friend')) {
            $basic->addField(
                Builder::friendPicker('users')
                    ->label(__p('friend::phrase.invite_friends'))
                    ->placeholder(__p('friend::phrase.invite_friends'))
                    ->multiple(true)
                    ->endpoint(url_utility()->makeApiUrl('friend'))
            );
        }

        $this->addDefaultFooter();
    }
}
