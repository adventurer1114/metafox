<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Group\Models\Category;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class CreateMobileForm.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateMobileForm extends AbstractForm
{
    public const MAX_TEXT_LENGTH = 3000;

    protected function prepare(): void
    {
        $categoryRepository = resolve(CategoryRepositoryInterface::class);
        $categoryDefault    = $categoryRepository->getCategoryDefault();
        $values             = ['reg_method' => PrivacyTypeHandler::PUBLIC];

        if ($categoryDefault?->is_active == Category::IS_ACTIVE) {
            Arr::set($values, 'category_id', $categoryDefault->entityId());
        }

        $this->asPost()
            ->title(__p('group::phrase.new_group'))
            ->action(Model::API_URL)
            ->setValue($values);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic(['label' => __p('group::phrase.group_info')]);

        $minGroupNameLength = Settings::get('group.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxGroupNameLength = Settings::get('group.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);

        $basic->addFields(
            Builder::text('name')
                ->required()
                ->label(__p('group::phrase.group_name'))
                ->placeholder(__p('group::phrase.fill_in_a_name_for_your_group'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxGroupNameLength]))
                ->maxLength($maxGroupNameLength)
                ->yup(
                    Yup::string()
                        ->required()
                        ->minLength($minGroupNameLength)
                        ->maxLength($maxGroupNameLength)
                ),
            Builder::richTextEditor('text')
                ->label(__p('core::phrase.description'))
                ->asMultiLine()
                ->placeholder(__p('core::phrase.add_some_description_to_your_type', ['type' => 'group'])),
            Builder::category('category_id')
                ->label(__p('core::phrase.category'))
                ->setRepository(CategoryRepositoryInterface::class)
                ->multiple(false)
                ->valueType('number')
                ->required()
                ->yup(
                    Yup::number()->required()
                ),
        );

        $section = $this->addSection(__p('group::phrase.group_privacy'))
            ->label(__p('group::phrase.group_privacy'));
        $section->addField(
            Builder::radioGroup('reg_method')
                ->required()
                ->label(__p('group::phrase.group_privacy'))
                ->placeholder(__p('core::phrase.group_privacy'))
                ->options($this->getRegOptions())
                ->yup(
                    Yup::string()->required()
                ),
        );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getRegOptions(): array
    {
        return [
            [
                'value' => PrivacyTypeHandler::PUBLIC,
                'label' => __p('group::phrase.public'),
            ], [
                'value' => PrivacyTypeHandler::CLOSED,
                'label' => __p('group::phrase.closed'),
            ], [
                'value' => PrivacyTypeHandler::SECRET,
                'label' => __p('group::phrase.secret'),
            ],
        ];
    }
}
