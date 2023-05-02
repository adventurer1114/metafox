<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Photo\Http\Resources\v1\Photo;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Photo\Http\Requests\v1\Photo\UploadFormRequest;
use MetaFox\Photo\Models\Photo as Model;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Policies\PhotoPolicy;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Repositories\CategoryRepositoryInterface;
use MetaFox\Photo\Support\Traits\MultipleTypeUploadTrait;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Yup\Shape;
use MetaFox\Yup\Yup;

/**
 * Class UploadPhotoForm.
 *
 * @property Model $resource
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class UploadPhotoForm extends AbstractForm
{
    use MultipleTypeUploadTrait;

    /**
     * @var int|null
     */
    protected ?int $ownerId = null;
    protected $owner        = null;

    /**
     * @throws AuthorizationException | AuthenticationException
     */
    public function boot(UploadFormRequest $request): void
    {
        $context = user();

        $params = $request->validated();

        policy_authorize(PhotoPolicy::class, 'create', $context);

        $this->ownerId = Arr::get($params, 'owner_id', 0);

        $this->owner = $this->ownerId == 0 ? $context : UserEntity::getById($this->ownerId)->detail;
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context = user();
        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'photo.item_privacy');

        $privacyAlbum = UserPrivacy::getItemPrivacySetting($context->entityId(), 'photo_album.item_privacy');

        $defaultCategory = Settings::get('photo.default_category');

        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }

        if ($privacyAlbum === false) {
            $privacyAlbum = MetaFoxPrivacy::EVERYONE;
        }

        $title = $this->getLabel($this->getAcceptableTypes());

        $this->title($title)
            ->submitAction('@photo/uploadMultiPhotos/submit')
            ->setBackProps(__p('core::web.photos'))
            ->asPost()
            ->action(url_utility()->makeApiUrl('/photo'))
            ->setValue([
                'add_new_album'         => 0,
                'module_id'             => 'photo',
                'privacy'               => $privacy,
                'owner_id'              => $this->ownerId,
                'new_album_privacy'     => $privacyAlbum,
                'files'                 => [],
                'categories'            => [$defaultCategory],
                'can_set_album_privacy' => $context->hasPermissionTo('photo_album.set_privacy'),
            ]);
    }

    protected function initialize(): void
    {
        $context           = user();
        $albumInfo         = Builder::section('albumInfo')->component(MetaFoxForm::CONTAINER);
        $types             = $this->getAcceptableTypes();
        $accept            = 'image/*';
        $acceptWhen        = $this->getAcceptableMimeTypes($types);
        $isVideoAllowed    = $this->canUploadVideos();
        $maxMediaPerUpload = $context->getPermissionValue('photo.maximum_number_of_media_per_upload');
        $maxPhotoSize      = file_type()->getFilesizePerType('photo');
        $maxVideoSize      = file_type()->getFilesizePerType('video');

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::uploadMultiMedia('files')
                ->required()
                ->accepts($accept)
                ->isVideoUploadAllowed($isVideoAllowed)
                ->acceptWhen([
                    $acceptWhen => [
                        'or',
                        ['eq', 'add_new_album', 1],
                        ['truthy', 'album'],
                    ],
                ])
                ->acceptFail(__p('photo::phrase.photo_accept_type_fail'))
                ->label($this->getLabel($types))
                ->placeholder(__p(
                    'photo::phrase.upload_multiple_photo_placeholder',
                    ['allowVideo' => (int) $isVideoAllowed]
                ))
                ->description(
                    __p('photo::phrase.upload_multiple_photo_description', [
                        'allowVideo'        => (int) $isVideoAllowed,
                        'maxPhotoSize'      => file_type()->getFilesizeReadableString($maxPhotoSize),
                        'maxVideoSize'      => file_type()->getFilesizeReadableString($maxVideoSize),
                        'maxMediaPerUpload' => $maxMediaPerUpload,
                    ])
                )
                ->yup(
                    $this->fileUploadValidator()
                ),
            Builder::album('album')
                ->multiple(false)
                ->sizeLarge()
                ->fullWidth()
                ->enableWhen(['falsy', 'add_new_album'])
                ->label(__p('photo::phrase.photo_album'))
                ->setOwner($this->owner)
                ->setUser($context)
                ->description(__p('photo::phrase.you_need_to_select_an_album', [
                    'allowVideo' => (int) $isVideoAllowed,
                ]))
                ->setRepository(AlbumRepositoryInterface::class),
            Builder::button('add_new_album')
                ->component('AddNewAlbum')
                ->showWhen(['truthy', $this->canCreateAlbum($context)])
                ->label(__p('photo::phrase.cancel_new_album'))
                ->setAttribute('cancelLabel', __p('photo::phrase.add_new_album'))
                ->variant('link')
                ->sizeSmall(),
            $albumInfo,
            Builder::privacy()
                ->fullWidth(false)
                ->width(250)
                ->label(__p('photo::phrase.photo_privacy'))
                ->description(__p('photo::phrase.photo_privacy_description'))
                ->showWhen([
                    'and',
                    ['falsy', 'album'], ['eq', 'add_new_album', 0],
                ]),
        );

        if (Settings::get('photo.allow_photo_category_selection', true)) {
            $basic->addFields(
                Builder::category()
                    ->sizeLarge()
                    ->setRepository(CategoryRepositoryInterface::class)
                    ->width(250)
            );
        }

        $minAlbumNameLength = Settings::get(
            'photo.album.minimum_name_length',
            MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH
        );
        $maxAlbumNameLength = Settings::get(
            'photo.album.maximum_name_length',
            MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH
        );

        $albumInfo->addFields(
            Builder::text('new_album_name')
                ->label(__p('photo::phrase.album_name'))
                ->required()
                ->showWhen(['truthy', 'add_new_album'])
                ->yup(
                    Yup::string()
                        ->when(Yup::when('add_new_album')
                            ->is(1)
                            ->then(Yup::string()
                                ->required()))
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
            Builder::textArea('new_album_description')
                ->label(__p('photo::phrase.album_description'))
                ->required(false)
                ->showWhen(['truthy', 'add_new_album']),
            Builder::privacy('new_album_privacy')
                ->label(__p('photo::phrase.album_privacy'))
                ->fullWidth(false)
                ->required()
                ->width(250)
                ->showWhen(['and', ['truthy', 'add_new_album'], ['truthy', 'can_set_album_privacy']]),
        );

        $this->addFooter()
            ->addFields(
                Builder::submit()->label(__p('core::phrase.upload')),
                Builder::cancelButton('_cancel'),
            );
    }

    /**
     * @param  string[]    $types
     * @return string|null
     */
    public function getLabel(array $types): ?string
    {
        $isVideoAllow = in_array('video', $types);

        return __p('photo::phrase.add_photos', ['allowVideo' => (int) $isVideoAllow]);
    }

    private function canCreateAlbum(User $context): bool
    {
        $owner = null;
        if ($this->ownerId != 0) {
            $owner = UserEntity::getById($this->ownerId)->detail;
        }

        return policy_check(AlbumPolicy::class, 'create', $context, $owner);
    }

    protected function fileUploadValidator(): Shape
    {
        $context           = user();
        $maxMediaPerUpload = $context->getPermissionValue('photo.maximum_number_of_media_per_upload');

        $validator = Yup::array()
            ->required(__p('photo::validation.media_files_are_required'))
            ->min(1, __p('photo::validation.media_files_are_required'));

        if ($maxMediaPerUpload) {
            $validator->max($maxMediaPerUpload, __p('photo::phrase.maximum_per_upload_limit_reached', [
                'limit' => (int) $maxMediaPerUpload,
            ]));
        }

        return $validator;
    }
}
