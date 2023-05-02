<?php

namespace MetaFox\Photo\Http\Resources\v1\Album;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Photo\Http\Requests\v1\Album\CreateFormRequest;
use MetaFox\Photo\Models\Album as Model;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateAlbumForm.
 * @property Model $resource
 * @driverName photo_album.store
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateAlbumForm extends AbstractForm
{
    /**
     * @var null
     */
    protected ?User $owner = null;

    /**
     * @var bool
     */
    protected bool $allowVideo = false;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function boot(CreateFormRequest $request): void
    {
        $context = $owner = user();

        $params = $request->validated();

        $ownerId = Arr::get($params, 'owner_id');

        if ($ownerId > 0) {
            $owner = UserEntity::getById($ownerId)->detail;
        }

        policy_authorize(AlbumPolicy::class, 'create', $context, $owner);

        $this->resource = new Model($params);

        $this->owner = $owner;

        $this->allowVideo = $this->allowUploadVideo($context);
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context = user();

        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'photo_album.item_privacy');

        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }

        $this->title(__p('photo::phrase.create_new_photo_album'))
            ->action(url_utility()->makeApiUrl('photo-album'))
            ->setBackProps(__p('core::web.photos'))
            ->asPost()
            ->submitAction('@album/uploadMultiAlbumItems/submit')
            ->setValue([
                'privacy'       => $privacy,
                'owner_id'      => $this->resource->owner_id ?? 0,
                'text'          => '', // set default value to prevent dirty
                'title'         => '',
                'items'         => [],
                'canSetPrivacy' => $context->hasPermissionTo('photo_album.set_privacy'),
            ]);
    }

    protected function initialize(): void
    {
        $context = user();
        $basic   = $this->addBasic();

        $minAlbumNameLength = Settings::get(
            'photo.album.minimum_name_length',
            MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH
        );
        $maxAlbumNameLength = Settings::get(
            'photo.album.maximum_name_length',
            MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH
        );

        $basic->addFields(
            Builder::text('name')
                ->required()
                ->returnKeyType('next')
                ->marginNormal()
                ->label(__p('core::phrase.name'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxAlbumNameLength]))
                ->maxLength($maxAlbumNameLength)
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_required'))
                        ->minLength(
                            $minAlbumNameLength,
                            __p(
                                'core::validation.field_minimum_length_of_characters',
                                [
                                    'number' => $minAlbumNameLength,
                                    'field'  => '${path}',
                                ]
                            )
                        )
                        ->maxLength(
                            $maxAlbumNameLength,
                            __p('core::validation.field_maximum_length_of_characters', [
                                'min'   => $minAlbumNameLength,
                                'max'   => $maxAlbumNameLength,
                                'field' => '${path}',
                            ])
                        )
                ),
        );

        $basic = $this->buildUploadField($basic);

        $basic->addFields(
            Builder::textArea('text')
                ->required(false)
                ->returnKeyType('default')
                ->label(__p('core::phrase.description')),
            Builder::privacy('privacy')
                ->label(__p('photo::phrase.album_privacy'))
                ->showWhen([
                    'and',
                    ['truthy', 'canSetPrivacy'],
                    [
                        'or',
                        [
                            'falsy',
                            'owner_id',
                        ], [
                            'eq',
                            'owner_id',
                            $context->entityId(),
                        ],
                    ],
                ])
                ->description(__p('photo::phrase.description_for_privacy_field')),
            Builder::hidden('owner_id'),
        );

        $this->buildFooter();
    }

    /**
     * @throws AuthenticationException
     */
    protected function buildUploadField(Section $basic): Section
    {
        $context = user();

        if (!app('events')->dispatch('photo.album.can_upload_to_album', [$context, $this->owner, 'photo'], true)) {
            return $basic;
        }

        $types = ['photo'];

        if ($this->allowVideo) {
            $types[] = 'video';
        }

        $basic->addField(
            Builder::uploadMultiAlbumItem('items')
                ->showWhen(['and', ['eq', 'owner_id', 0]])
                ->allowTypes($types)
                ->dialogTitle(__p('photo::phrase.add_photos', ['allowVideo' => (int) $this->allowVideo]))
                ->required()
        );

        return $basic;
    }

    protected function allowUploadVideo(User $context): bool
    {
        if (!Settings::get('photo.photo_allow_uploading_video_to_photo_album', true)) {
            return false;
        }

        if (!app('events')->dispatch('photo.album.can_upload_to_album', [$context, $this->owner, 'video'], true)) {
            return false;
        }

        return true;
    }

    private function buildFooter(): void
    {
        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->label(__p('photo::phrase.create_album')),
                Builder::cancelButton(),
            );
    }
}
