<?php

namespace MetaFox\Photo\Http\Resources\v1\Album;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Photo\Http\Requests\v1\Album\CreateFormRequest;
use MetaFox\Photo\Models\Album as Model;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Support\Facades\Album as FacadesAlbum;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreAlbumMobileForm.
 * @property Model $resource
 *
 * @driverType form-mobile
 * @driverName photo.album.store
 */
class StoreAlbumMobileForm extends AbstractForm
{
    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(CreateFormRequest $request, AlbumRepositoryInterface $repository, ?int $id = null): void
    {
        $context = user();
        $params  = $request->validated();
        $params  = Arr::add($params, 'user_id', $context->entityId());
        $params  = Arr::add($params, 'user_type', $context->entityType());

        policy_authorize(AlbumPolicy::class, 'create', $context);
        $this->resource = new Model($params);
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
            ->asPost()
            ->setValue([
                'privacy'       => $privacy,
                'owner_id'      => $this->resource->owner_id ?? 0,
                'text'          => '', // set default value to prevent dirty
                'title'         => '',
                'canSetPrivacy' => $context->hasPermissionTo('photo_album.set_privacy'),
            ]);
    }

    protected function initialize(): void
    {
        $context        = user();
        $basic          = $this->addBasic();
        $minLength      = Settings::get('photo.album.minimum_name_length');
        $maxLength      = Settings::get('photo.album.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);
        $isDefaultAlbum = $this->resource instanceof Model && FacadesAlbum::isDefaultAlbum($this->resource->album_type);

        $basic->addFields(
            Builder::text('name')
                ->required()
                ->returnKeyType('next')
                ->disabled($isDefaultAlbum)
                ->marginNormal()
                ->maxLength($maxLength)
                ->label(__p('core::phrase.name'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxLength]))
                ->yup(
                    Yup::string()
                        ->minLength($minLength)
                        ->maxLength($maxLength)
                        ->required(__p('validation.this_field_is_required'))
                ),
            Builder::richTextEditor('text')
                ->required(false)
                ->returnKeyType('default')
                ->label(__p('core::phrase.description')),
            Builder::privacy('privacy')
                ->label(__p('photo::phrase.album_privacy'))
                ->description(__p('photo::phrase.description_for_privacy_field'))
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
                ]),
            Builder::hidden('owner_id'),
        );
    }
}
