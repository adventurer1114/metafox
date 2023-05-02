<?php

namespace MetaFox\Music\Http\Resources\v1\Song;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\PrivacyFieldTrait;
use MetaFox\Music\Http\Requests\v1\Song\CreateFormRequest;
use MetaFox\Music\Models\Song as Model;
use MetaFox\Music\Policies\SongPolicy;
use MetaFox\Music\Repositories\GenreRepositoryInterface;
use MetaFox\Music\Repositories\SongRepositoryInterface;
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
 * Class StoreSongForm.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreSongForm extends AbstractForm
{
    use PrivacyFieldTrait {
        PrivacyFieldTrait::buildPrivacyField as buildPrivacyFieldTrait;
    }

    public bool $preserveKeys = true;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(CreateFormRequest $request, SongRepositoryInterface $repository, ?int $id = null): void
    {
        $context = user();
        $params  = $request->validated();
        $this->setOwner($context);
        if ($params['owner_id'] != 0) {
            $userEntity = UserEntity::getById($params['owner_id']);
            $this->setOwner($userEntity->detail);
        }

        policy_authorize(SongPolicy::class, 'create', $context, $this->owner);
        $this->resource = new Model($params);
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context = user();

        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'music_song.item_privacy');

        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }

        $data = [
            'module_id'    => 'music',
            'privacy'      => $privacy,
            'useThumbnail' => true,
            'owner_id'     => $this->resource->owner_id,
        ];

        $defaultGenre = Settings::get('music.music_song.song_default_genre');

        if ($defaultGenre != null) {
            Arr::set($data, 'genres', [$defaultGenre]);
        }

        $this->title(__p('music::phrase.add_new_music'))
            ->action(url_utility()->makeApiUrl('music/song'))
            ->asPost()
            ->setBackProps(__p('core::web.music'))
            ->setValue($data);
    }

    protected function initialize(): void
    {
        $basic             = $this->addBasic();
        $minSongNameLength = Settings::get(
            'music.music_song.minimum_name_length',
            MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH
        );
        $maxSongNameLength = Settings::get(
            'music.music_song.maximum_name_length',
            MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH
        );
        $maxSongSize  = file_type()->getFilesizePerType('music');
        $privacyField = $this->buildPrivacyField();

        if (!$this->isEdit()) {
            $basic->addFields(
                Builder::singleAudio()
                    ->required()
                    ->itemType('music_song')
                    ->label(__p('music::phrase.upload'))
                    ->placeholder(__p('music::phrase.select_music'))
                    ->maxUploadFileSize(Settings::get('storage.filesystems.max_upload_filesize'))
                    ->description(__p('music::phrase.select_music_field_description', [
                        'maxSongSize'      => file_type()->getFilesizeReadableString($maxSongSize),
                        'maxSongPerUpload' => 0,
                    ]))
                    ->storageId('music')
                    ->yup(
                        Yup::object()
                            ->required(__p('music::validation.file_is_a_required_field'))
                            ->addProperty(
                                'id',
                                Yup::number()
                                    ->required(__p('music::validation.file_is_a_required_field'))
                            )
                    )
            );
        }

        $basic->addFields(
            Builder::text('name')
                ->required()
                ->marginNormal()
                ->label(__p('music::phrase.song_title'))
                ->placeholder(__p('music::phrase.fill_in_a_name_for_your_song'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxSongNameLength]))
                ->maxLength($maxSongNameLength)
                ->yup(
                    Yup::string()
                        ->required()
                        ->minLength(
                            $minSongNameLength,
                            __p(
                                'core::validation.title_minimum_length_of_characters',
                                ['number' => $minSongNameLength]
                            )
                        )
                        ->maxLength(
                            $maxSongNameLength,
                            __p('core::validation.title_maximum_length_of_characters', [
                                'min' => $minSongNameLength,
                                'max' => $maxSongNameLength,
                            ])
                        )
                ),
            Builder::richTextEditor('description')
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('music::phrase.add_some_description_to_your_song')),
            $this->buildThumbnailField(),
            Builder::attachment()
                ->itemType('music_song'),
            Builder::category('genres')
                ->required()
                ->multiple(true)
                ->label(__p('music::phrase.genres'))
                ->sizeLarge()
                ->setRepository(GenreRepositoryInterface::class)
                ->yup(
                    Yup::array()
                        ->min(1, __p('music::validation.genres_is_a_required_field'))
                ),
            Builder::hidden('module_id'),
            Builder::hidden('owner_id'),
            $privacyField,
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

        // force returnUrl as string
        $basic->addField(
            Builder::hidden('returnUrl')
        );
    }

    public function isEdit(): bool
    {
        return null !== $this->resource?->id;
    }

    private function checkBelongToAlbum(): bool
    {
        if (!$this->resource->album) {
            return false;
        }

        return true;
    }

    private function buildThumbnailField(): ?AbstractField
    {
        if ($this->checkBelongToAlbum()) {
            return null;
        }

        return Builder::singlePhoto('thumbnail')
            ->widthPhoto('160px')
            ->label(__p('music::phrase.cover_photo'))
            ->aspectRatio('1:1')
            ->itemType('music_song')
            ->previewUrl($this->resource?->image_file_id ? $this->resource?->image : '')
            ->showWhen([
                'or',
                ['neq', 'file', null], ['truthy', 'useThumbnail'],
            ]);
    }

    protected function buildPrivacyField(): ?AbstractField
    {
        if ($this->checkBelongToAlbum()) {
            return null;
        }

        return $this->buildPrivacyFieldTrait()
            ->description(__p('music::phrase.control_who_can_see_this_song'));
    }
}
