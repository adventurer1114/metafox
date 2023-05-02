<?php

namespace MetaFox\Video\Http\Resources\v1\Video;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\PrivacyFieldTrait;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Video\Http\Requests\v1\Video\CreateFormRequest;
use MetaFox\Video\Models\Video as Model;
use MetaFox\Video\Policies\VideoPolicy;
use MetaFox\Video\Repositories\CategoryRepositoryInterface;
use MetaFox\Video\Repositories\VideoRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateVideoForm.
 * @property ?Model $resource
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateVideoForm extends AbstractForm
{
    use PrivacyFieldTrait;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(CreateFormRequest $request, VideoRepositoryInterface $repository, ?int $id = null): void
    {
        $context = user();
        $params  = $request->validated();

        if ($params['owner_id'] != 0) {
            $userEntity = UserEntity::getById($params['owner_id']);
            $this->setOwner($userEntity->detail);
        }

        policy_authorize(VideoPolicy::class, 'create', $context, $this->owner);
        $this->resource = new Model($params);
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context = user();

        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'video.item_privacy');

        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }

        $defaultCategory = Settings::get('video.default_category');

        $this->title(__p('video::phrase.share_a_video'))
            ->action(url_utility()->makeApiUrl('video'))
            ->setBackProps(__p('video::phrase.videos'))
            ->asPost()
            ->setValue([
                'module_id'    => 'video',
                'privacy'      => $privacy,
                'owner_id'     => 0,
                'useThumbnail' => false,
                'categories'   => $defaultCategory ? [$defaultCategory] : [],
            ]);
    }

    protected function initialize(): void
    {
        $minVideoNameLength = Settings::get('video.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);

        $maxVideoNameLength = Settings::get('video.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);

        $basic = $this->addBasic();

        if (!$this->isEdit()) {
            $basic->addFields(
                Builder::singleVideo()
                    ->required()
                    ->itemType('video')
                    ->label(__p('video::phrase.select_video'))
                    ->description(__p('video::phrase.select_video_field_description'))
                    ->yup(
                        Yup::object()
                            ->required(__p('video::validation.video_file_is_a_required_field'))
                            ->addProperty(
                                'id',
                                Yup::number()
                                    ->required(__p('video::validation.video_file_is_a_required_field'))
                            )
                    ),
            );
        }
        $basic->addFields(
            Builder::text('title')
                ->required()
                ->label(__p('core::phrase.title'))
                ->placeholder(__p('video::phrase.fill_in_a_title_for_your_video'))
                ->maxLength($maxVideoNameLength)
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxVideoNameLength]))
                ->yup(
                    Yup::string()->required(__p('validation.this_field_is_a_required_field'))
                        ->minLength($minVideoNameLength)
                        ->maxLength($maxVideoNameLength)
                ),
            Builder::singlePhoto('thumbnail')
                ->widthPhoto('33%')
                ->aspectRatio('16:9')
                ->required(false)
                ->itemType('photo')
                ->previewUrl($this->resource?->thumbnail_file_id ? $this->resource?->image : '')
                ->showWhen([
                    'or',
                    ['neq', 'file', null], ['truthy', 'useThumbnail'],
                ])
                ->label(__p('video::phrase.video_thumbnail'))
                ->placeholder(__p('video::phrase.video_thumbnail_placeholder')),
            Builder::richTextEditor('text')
                ->required(false)
                ->returnKeyType('default')
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('video::phrase.add_some_content_to_your_video')),
            Builder::category('categories')
                ->multiple(true)
                ->sizeLarge()
                ->fullWidth()
                ->minWidth(275)
                ->setRepository(CategoryRepositoryInterface::class),
            Builder::hidden('module_id')
                ->setValue('video'),
            Builder::hidden('owner_id'),
            $this->buildPrivacyFieldForVideo()
        );

        $submitLabel = __p('core::phrase.upload');

        if ($this->isEdit()) {
            $submitLabel = __p('core::phrase.save_changes');
        }

        $this->addFooter()
            ->addFields(
                Builder::submit('__submit')->label($submitLabel),
                Builder::cancelButton()->sizeMedium(),
            );
    }

    protected function buildPrivacyFieldForVideo(): AbstractField
    {
        $owner  =  $this->resource?->ownerId();
        if (!$owner) {
            $owner = 0;
        }
        $defaultAlbums = app('events')->dispatch('photo.album.get_default', [$owner], true);
        $albumId       = $this->resource?->album_id;

        if ($this->isEdit()) {
            if (in_array($albumId, $defaultAlbums->pluck('id')->toArray())) {
                return Builder::hidden('privacy');
            }

            if ($albumId != 0) {
                return Builder::hidden('privacy');
            }
        }

        return $this->buildPrivacyField()
            ->description(__p('video::phrase.control_who_can_see_this_video'))
            ->fullWidth(false)
            ->minWidth(275);
    }

    public function isEdit(): bool
    {
        return null !== $this->resource?->id;
    }
}
