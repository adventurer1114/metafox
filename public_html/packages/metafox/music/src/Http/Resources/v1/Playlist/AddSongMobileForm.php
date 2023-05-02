<?php

namespace MetaFox\Music\Http\Resources\v1\Playlist;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Music\Models\Playlist as Model;
use MetaFox\Music\Repositories\PlaylistRepositoryInterface;
use MetaFox\Music\Repositories\SongRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class AddSongMobileForm.
 */
class AddSongMobileForm extends AbstractForm
{
    /**
     * @var Model
     */
    public $resource;

    private string $itemId;
    private array $playlistIds = [];

    public function boot(Request $request): void
    {
        $this->itemId = $request->get('item_id', 0);

        $song              = resolve(SongRepositoryInterface::class)->find($this->itemId);
        $this->playlistIds = $song->playlists->pluck('id')->toArray();
    }

    protected function prepare(): void
    {
        $this->title(__p('music::phrase.add_to_playlist'))
            ->action('music/playlist/add-song')
            ->asPost()
            ->setValue([
                'item_id'      => $this->itemId,
                'playlist_ids' => $this->playlistIds,
            ]);
    }

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $options = $this->getPlaylistOptions();

        if (!count($options)) {
            $this->addHeader(['showRightHeader' => false])->component('FormHeader');

            $basic->addField(
                Builder::typography('description')
                    ->plainText(__p('music::phrase.no_playlists_available', [
                        'link' => url_utility()->makeApiFullUrl('music/playlist/add'),
                        'can'  => 0,
                    ])),
            );

            return;
        }

        $basic->addFields(
            Builder::hidden('item_id')
                ->required(),
            Builder::choice('playlist_ids')
                ->required()
                ->disableClearable()
                ->multiple(true)
                ->options($options)
                ->label(__p('music::phrase.choose_playlist'))
                ->valueType('numeric'),
        );
    }

    /**
     * @return array<int,              mixed>
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    protected function getPlaylistOptions(): array
    {
        return resolve(PlaylistRepositoryInterface::class)->getPlaylistOptions(user());
    }
}
