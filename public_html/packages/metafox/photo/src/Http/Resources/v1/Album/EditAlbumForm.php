<?php

namespace MetaFox\Photo\Http\Resources\v1\Album;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Photo\Http\Requests\v1\Album\CreateFormRequest;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Support\Facades\Album as FacadesAlbum;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditAlbumForm.
 * @driverName photo_album.update
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EditAlbumForm extends AbstractForm
{
    /**
     * @var bool
     */
    protected bool $isAllowedUploadItem;

    /**
     * @var bool
     */
    protected bool $allowVideo = false;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function boot(CreateFormRequest $request, AlbumRepositoryInterface $repository, ?int $id = null): void
    {
        $context = user();

        $this->resource = $repository->find($id);

        policy_authorize(AlbumPolicy::class, 'update', $context, $this->resource);

        $this->isAllowedUploadItem = policy_check(AlbumPolicy::class, 'uploadMedias', $context, $this->resource);

        $this->allowVideo = $this->allowUploadVideo($context);
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context   = user();
        $albumInfo = $this->resource->albumInfo;

        $privacy = $this->resource->privacy;

        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $description = '';
        if ($albumInfo) {
            $description = $albumInfo->description;
        }

        $name = $this->resource->name;
        if (FacadesAlbum::isDefaultAlbum($this->resource->album_type)) {
            $name = FacadesAlbum::getDefaultAlbumTitle($this->resource);
        }

        $items = $this->resource->items->map(function (\MetaFox\Photo\Models\AlbumItem $item) {
            if ($item->detail instanceof Content) {
                return ResourceGate::asResource($item->detail, 'item', false);
            }

            return null;
        });

        $this->title(__p('photo::phrase.edit_photo_album'))
            ->action(url_utility()->makeApiUrl("photo-album/{$this->resource->entityId()}"))
            ->setBackProps(__p('photo::phrase.photo_album'))
            ->asPut()
            ->submitAction('@album/uploadMultiAlbumItems/submit')
            ->setValue([
                'name'          => $name,
                'owner_id'      => $this->resource->owner_id,
                'owner_type'    => $this->resource->owner_type,
                'text'          => $description,
                'privacy'       => $privacy,
                'items'         => $items,
                'canSetPrivacy' => $context->hasPermissionTo('photo_album.set_privacy'),
            ]);
    }

    protected function initialize(): void
    {
        $isDefaultAlbum = FacadesAlbum::isDefaultAlbum($this->resource->album_type);

        $minAlbumNameLength = Settings::get(
            'photo.album.minimum_name_length',
            MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH
        );

        $maxAlbumNameLength = Settings::get(
            'photo.album.maximum_name_length',
            MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH
        );

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('name')
                ->required()
                ->returnKeyType('next')
                ->marginNormal()
                ->label(__p('core::phrase.name'))
                ->disabled($isDefaultAlbum)
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

        $this->buildUploadField($basic);

        $basic->addFields(
            Builder::textArea('text')
                ->required(false)
                ->returnKeyType('default')
                ->label(__p('core::phrase.description')),
            Builder::privacy('privacy')
                ->disabled($isDefaultAlbum)
                ->description(__p('photo::phrase.description_for_privacy_field'))
                ->showWhen([
                    'and',
                    ['truthy', 'canSetPrivacy'],
                    ['eq', 'owner_id', $this->resource->userId()],
                ]),
            Builder::hidden('owner_id'),
        );

        $this->addDefaultFooter(true);
    }

    protected function buildUploadField(Section $basic): void
    {
        $field = Builder::uploadMultiAlbumItem('items')
            ->allowUploadItems($this->isAllowedUploadItem)
            ->allowRemoveItems($this->isAllowedUploadItem)
            ->required();

        if (!$this->isAllowedUploadItem) {
            $basic->addField($field);

            return;
        }

        $types = ['photo'];

        if ($this->allowVideo) {
            $types[] = 'video';
        }

        $field->allowTypes($types)
            ->dialogTitle(__p('photo::phrase.add_photos', ['allowVideo' => (int) $this->allowVideo]));

        $basic->addField($field);
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
}
