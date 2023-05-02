<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Group\Models\Category;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class CreateForm.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateForm extends AbstractForm
{
    public const MAX_TEXT_LENGTH = 3000;

    protected function prepare(): void
    {
        $categoryRepository = resolve(CategoryRepositoryInterface::class);
        $categoryDefault    = $categoryRepository->getCategoryDefault();
        $values             = [];

        if ($categoryDefault?->is_active == Category::IS_ACTIVE) {
            $values = ['category_id' => $categoryDefault->entityId()];
        }

        $this->asPost()
            ->setBackProps(__p('core::web.groups'))
            ->title(__p('group::phrase.create_group'))
            ->action(Model::API_URL)
            ->navigationConfirmation([
                'title'          => __p('core::web.leave_page'),
                'message'        => __p('group::phrase.if_you_leave_now_your_group_wont_be_created_and_your_progress_wont_be_saved'),
                'negativeButton' => [
                    'label' => __p('group::phrase.stay_on_page'),
                ],
                'positiveButton' => [
                    'label' => __p('core::web.leave'),
                ],
            ])->setValue($values);
    }

    protected function initialize(): void
    {
        $basic              = $this->addBasic();
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
            Builder::textArea('text')
                ->maxLength(self::MAX_TEXT_LENGTH)
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('core::phrase.add_some_description_to_your_type', ['type' => 'group'])),
            Builder::category('category_id')
                ->label(__p('core::phrase.category'))
                ->multiple(false)
                ->required()
                ->setRepository(CategoryRepositoryInterface::class)
                ->valueType('number')
                ->sx(['width' => 275])
                ->yup(
                    Yup::number()->required()
                ),
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

        $basic->addField(
            Builder::choice('reg_method')
                ->required()
                ->label(__p('group::phrase.group_privacy'))
                ->options($this->getRegOptions())
                ->sx(['width' => 275])
                ->yup(
                    Yup::string()->required()
                ),
        );

        $this->addDefaultFooter();
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
