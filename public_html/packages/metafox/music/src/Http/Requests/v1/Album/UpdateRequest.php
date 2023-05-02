<?php

namespace MetaFox\Music\Http\Requests\v1\Album;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Music\Rules\Mp3FileRule;
use MetaFox\Music\Rules\UpdateSongAlbumRule;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Rules\ResourceNameRule;
use MetaFox\Platform\Rules\ResourceTextRule;
use MetaFox\Platform\Rules\ValidImageRule;
use MetaFox\Platform\Traits\Http\Request\AttachmentRequestTrait;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Music\Http\Controllers\Api\v1\AlbumController::update;
 * stub: api_action_request.stub
 */

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    use PrivacyRequestTrait;
    use AttachmentRequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function rules(): array
    {
        $rules = [
            'name'      => ['required', 'string', new ResourceNameRule('music.music_album')],
            'text'      => ['sometimes', 'nullable', 'string', new ResourceTextRule(true)],
            'year'      => ['required', 'digits:4', 'integer', 'min:1900', 'max:' . (int) Carbon::now()->addYear()->format('Y')],
            'thumbnail' => ['sometimes', resolve(ValidImageRule::class)],
            'genres'    => ['required', 'array'],
            'genres.*'  => ['required_with:genres', 'numeric', new ExistIfGreaterThanZero('exists:music_genres,id')],
            'owner_id'  => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
            'privacy'   => ['required', new PrivacyRule()],
        ];

        $rules = $this->applyAttachmentRules($rules);

        $this->handleSongRule($rules);

        return $rules;
    }

    /**
     * @throws AuthenticationException
     */
    private function handleSongRule(array &$rules): void
    {
        $maxSongPerUpload = user()->getPermissionValue('music_song.maximum_number_of_songs_per_upload');

        $rule = [
            'songs'               => ['sometimes', 'array', new UpdateSongAlbumRule($maxSongPerUpload)],
            'songs.*'             => ['required_with:songs', resolve(Mp3FileRule::class)],
            'songs.*.name'        => ['required', 'string', new ResourceNameRule('music.music_album')],
            'songs.*.description' => ['sometimes', 'string', 'nullable'],
        ];

        $rules = array_merge($rules, $rule);
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = $this->handlePrivacy($data);

        if (!array_key_exists('genres', $data)) {
            $data['genres'] = [Settings::get('music.music_song.song_default_genre')];
        }

        $this->handleSongAttribute($data);

        if (array_key_exists('songs', $data)) {
            $data['songs'] = $this->handleAlbumItems($data['songs']);
        }

        $data['thumb_temp_file']  = Arr::get($data, 'thumbnail.temp_file', 0);
        $data['remove_thumbnail'] = Arr::get($data, 'thumbnail.status', false);

        return $data;
    }

    private function handleSongAttribute(array &$data): void
    {
        $genres  = Arr::get($data, 'genres', []);
        $thumb   = Arr::get($data, 'thumb_temp_file');
        $privacy = Arr::get($data, 'privacy');

        $songs = [];

        foreach (Arr::get($data, 'songs', []) as $song) {
            if (!Arr::has($song, 'status')) {
                Arr::set($song, 'status', MetaFoxConstant::FILE_UPDATE_STATUS);
            }

            $song['genres']          = $genres;
            $song['thumb_temp_file'] = $thumb;
            $song['privacy']         = $privacy;

            if ($privacy == MetaFoxPrivacy::CUSTOM) {
                Arr::set($song, 'list', $privacy);
            }

            $songs[] = $song;
        }

        if (!count($songs)) {
            unset($data['songs']);

            return;
        }

        Arr::set($data, 'songs', $songs);
    }

    protected function handleAlbumItems(array $items): array
    {
        return collect($items)
            ->values()
            ->groupBy('status')
            ->toArray();
    }
}
