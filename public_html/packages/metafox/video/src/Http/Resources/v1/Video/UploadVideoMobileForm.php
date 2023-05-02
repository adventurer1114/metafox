<?php

namespace MetaFox\Video\Http\Resources\v1\Video;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
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
 * Class StoreVideoMobileForm.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @driverType form-mobile
 * @driverName video.video.upload
 */
class UploadVideoMobileForm extends AbstractForm
{
    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @driverType form-mobile
     * @driverName video.video.store
     */
    public function boot(CreateFormRequest $request, VideoRepositoryInterface $repository, ?int $id = null): void
    {
        $context = user();
        $params  = $request->validated();
        policy_authorize(VideoPolicy::class, 'create', $context);
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
            ->asPost()
            ->setValue([
                'module_id'    => 'video',
                'privacy'      => $privacy,
                'owner_id'     => $this->resource->owner_id,
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
                Builder::singleVideo('file')
                    ->required()
                    ->label(__p('video::phrase.select_video'))
                    ->description(__p('video::phrase.select_video_field_description'))
                    ->enableWhen(['falsy', 'video_url'])
                    ->yup(
                        Yup::object()->when(
                            Yup::when('video_url')
                                ->is('$empty')
                                ->then(Yup::object()
                                    ->required(__p('video::validation.video_file_is_required')))
                                ->otherwise(Yup::object()->nullable())
                        )
                    ),
                Builder::singleVideo('video_url')
                    ->required()
                    ->fileType('link')
                    ->label(__p('video::phrase.video_url'))
                    ->description(__p('video::phrase.select_video_field_description'))
                    ->enableWhen(['and', ['falsy', 'file'], ['falsy', 'thumbnail']])
                    ->yup(
                        Yup::when('file')
                            ->is('$empty')
                            ->then(Yup::string()
                                ->required(__p('video::validation.video_file_is_required')))
                            ->otherwise(Yup::string()->nullable())
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
                    Yup::string()->required(__p('validation.this_field_is_required'))
                        ->minLength($minVideoNameLength)
                        ->maxLength($maxVideoNameLength)
                ),
            Builder::singlePhoto('thumbnail')
                ->required(false)
                ->itemType('photo')
                ->previewUrl($this->resource?->thumbnail_file_id ? $this->resource?->image : '')
                ->showWhen([
                    'or',
                    ['falsy', 'video_url'],
                    ['neq', 'file', null],
                    ['truthy', 'useThumbnail'],
                ])
                ->label(__p('video::phrase.video_thumbnail'))
                ->placeholder(__p('video::phrase.video_thumbnail_placeholder')),
            Builder::textArea('text')
                ->required(false)
                ->returnKeyType('default')
                ->asMultiLine()
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('video::phrase.add_some_content_to_your_video')),
            Builder::category('categories')
                ->multiple(true)
                ->sizeLarge()
                ->fullWidth()
                ->setRepository(CategoryRepositoryInterface::class),
            $this->addPrivacyField()
        );
        $basic->addFields(
            Builder::hidden('module_id')
                ->setValue('video'),
            Builder::hidden('owner_id'),
        );
    }

    public function isEdit(): bool
    {
        return null !== $this->resource->id;
    }

    protected function addPrivacyField(): AbstractField
    {
        $context  = user();
        $showWhen = [
            'and',
            ['falsy', 'album'],
            [
                'or',
                ['falsy', 'owner_id'],
                ['eq', 'owner_id', $context->entityId()],
            ],
        ];

        if ($this->isEdit()) {
            $defaultAlbums = app('events')->dispatch('photo.album.get_default', [$this->resource->ownerId()], true);

            if (null !== $defaultAlbums && $defaultAlbums->count()) {
                $showWhen[1] = [
                    'or',
                    ['falsy', 'album'],
                    ['oneOf', 'album', $defaultAlbums->pluck('id')->toArray()],
                ];
            }
        }

        return Builder::privacy()
            ->description(__p('video::phrase.control_who_can_see_this_video'))
            ->fullWidth(false)
            ->minWidth(275)
            ->showWhen($showWhen);
    }
}
