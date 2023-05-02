<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Section;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Group\Repositories\GroupChangePrivacyRepositoryInterface;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class InfoForm.
 * @property Model $resource
 */
class InfoMobileForm extends AbstractForm
{
    public function boot(GroupRepositoryInterface $repository, ?int $id): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('group::phrase.group_info'))
            ->action("group/{$this->resource->entityId()}")
            ->setValue([
                'name'        => $this->resource->name,
                'type_id'     => $this->resource->type_id,
                'category_id' => $this->resource->category_id,
                'vanity_url'  => $this->resource->profile_name,
                'reg_method'  => $this->resource->privacy_type,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $minGroupNameLength = Settings::get('group.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxGroupNameLength = Settings::get('group.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);

        $basic->addFields(
            Builder::text('name')
                ->required()
                ->label(__p('group::phrase.group_name'))
                ->placeholder(__p('group::phrase.fill_in_a_name_for_your_group'))
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxGroupNameLength,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('group::phrase.group_name'),
                                'maxLength' => $maxGroupNameLength,
                            ])
                        )
                        ->minLength(
                            $minGroupNameLength,
                            __p('validation.field_must_be_at_least_min_length_characters', [
                                'field'     => __p('group::phrase.group_name'),
                                'minLength' => $minGroupNameLength,
                            ])
                        )
                ),
            Builder::category('category_id')
                ->required()
                ->label(__p('core::phrase.category'))
                ->setRepository(CategoryRepositoryInterface::class)
                ->multiple(false)
                ->valueType('number')
                ->yup(Yup::number()->required()),
            Builder::text('vanity_url')
                ->label(__p('core::phrase.url'))
                ->placeholder(__p('core::phrase.url'))
                ->description(__p('group::phrase.description_edit_group_url'))
                ->setAttribute('contextualDescription', url_utility()->makeApiFullUrl(''))
                ->findReplace([
                    'find'    => MetaFoxConstant::SLUGIFY_REGEX,
                    'replace' => '-',
                ]),
        );
    }

    protected function handleFieldPrivacy(Section $basic): AbstractField
    {
        $isPendingChangePrivacy = resolve(GroupChangePrivacyRepositoryInterface::class)
            ->isPendingChangePrivacy($this->resource);
        if ($isPendingChangePrivacy) {
            return $basic->addFields(
                Builder::typography('typography')
                    ->label(__p('group::phrase.waiting_for_changes_privacy_label'))
                    ->description(__p('group::phrase.waiting_for_changes_privacy_desc'))
                    ->color('text.secondary'),
            );
        }

        $builder = Builder::radioGroup('reg_method')
            ->required()
            ->label(__p('core::phrase.privacy'))
            ->placeholder(__p('core::phrase.privacy'))
            ->options($this->getRegOptions());

        if ($this->resource->isSecretPrivacy()) {
            $builder->setAttributes([
                'isCanEdit'        => false,
                'descriptionValue' => __p('group::phrase.change_privacy_group_secret_description'),
            ]);
        }

        return $basic->addFields($builder);
    }

    protected function getRegOptions(): array
    {
        $currentPrivacy = $this->resource->privacy_type;

        return [
            [
                'value'       => PrivacyTypeHandler::PUBLIC,
                'label'       => __p('group::phrase.public'),
                'description' => __p('group::phrase.anyone_can_see_the_group_its_members_and_their_posts'),
                'disabled'    => $this->checkDisabledPublicPrivacy($currentPrivacy),
            ], [
                'value'       => PrivacyTypeHandler::CLOSED,
                'label'       => __p('group::phrase.closed'),
                'description' => __p('group::phrase.anyone_can_find_the_group_and_see_who_s_in_it_only_members_can_see_posts'),
                'disabled'    => $this->checkDisabledClosedPrivacy($currentPrivacy),
            ], [
                'value'       => PrivacyTypeHandler::SECRET,
                'label'       => __p('group::phrase.secret'),
                'description' => __p('group::phrase.only_members_can_find_the_group_and_see_posts'),
            ],
        ];
    }

    protected function checkDisabledPublicPrivacy(int $privacy): bool
    {
        return $privacy != PrivacyTypeHandler::PUBLIC;
    }

    protected function checkDisabledClosedPrivacy(int $privacy): bool
    {
        return $privacy == PrivacyTypeHandler::SECRET;
    }
}
