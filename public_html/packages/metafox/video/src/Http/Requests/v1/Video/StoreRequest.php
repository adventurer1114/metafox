<?php

namespace MetaFox\Video\Http\Requests\v1\Video;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\CategoryRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Rules\ResourceNameRule;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;
use MetaFox\Video\Repositories\CategoryRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Video\Http\Controllers\Api\v1\VideoController::store;
 * stub: api_action_request.stub
 */

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
{
    use PrivacyRequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', new ResourceNameRule('video')],
            'text'                => ['sometimes', 'string', 'nullable'],
            'file'                => ['required_without:video_url'],
            'file.temp_file'      => ['required_with:file', 'numeric', 'exists:storage_files,id'],
            'thumbnail'           => ['sometimes', 'array'],
            'thumbnail.temp_file' => ['required_with:thumbnail', 'numeric', 'exists:storage_files,id'],
            'video_url'           => ['required_without:file', 'url', 'nullable', 'exclude_with:file'],
            'categories'          => ['sometimes', 'array'],
            'categories.*'        => ['numeric', new CategoryRule(resolve(CategoryRepositoryInterface::class))],
            'owner_id'            => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
            'privacy'             => ['required', new PrivacyRule()],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = $this->handlePrivacy($data);

        $data['temp_file']       = Arr::get($data, 'file.temp_file', 0);
        $data['thumb_temp_file'] = Arr::get($data, 'thumbnail.temp_file', 0);

        if (empty($data['owner_id'])) {
            $data['owner_id'] = 0;
        }

        if (array_key_exists('video_url', $data)) {
            $linkData = $this->validateLink($data['video_url']);
            $data     = array_merge($linkData, $data);
        }

        if (!array_key_exists('categories', $data)) {
            $data['categories'][] = Settings::get('video.default_category');
        }

        return $data;
    }

    /**
     * @param  string|null          $url
     * @return array<string, mixed>
     * @throws ValidationException
     */
    protected function validateLink(?string $url): array
    {
        if (!$url) {
            return [];
        }

        $data = app('events')->dispatch('core.parse_url', [$url], true);

        // The parsed link should be a valid video link
        $isVideo = $data['is_video'] ?? false;
        Validator::make(
            ['video_url' => $isVideo],   //data
            ['video_url' => 'accepted'], //rules
            ['accepted'  => __p('video::validation.invalid_video_link')] //error messages
        )->validated();

        return [
            'title'      => $data['title'] ?? null,
            'text'       => $data['description'] ?? null,
            'embed_code' => $data['embed_code'] ?? null,
            'duration'   => $data['duration'] ?? null,
            'thumbnail'  => $data['image'] ?? null,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required_without' => __p('video::validation.video_file_is_a_required_field'),
        ];
    }
}
