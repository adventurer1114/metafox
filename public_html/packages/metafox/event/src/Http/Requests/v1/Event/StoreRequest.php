<?php

namespace MetaFox\Event\Http\Requests\v1\Event;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MetaFox\Core\Rules\DateEqualOrAfterRule;
use MetaFox\Event\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\CategoryRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Rules\ResourceNameRule;
use MetaFox\Platform\Traits\Http\Request\AttachmentRequestTrait;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Event\Http\Controllers\Api\v1\EventController::store;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
{
    use PrivacyRequestTrait;
    use AttachmentRequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $fileRequired = 'sometimes';
        if (Settings::get('event.is_image_required', false)) {
            $fileRequired = 'required';
        }

        $rules = [
            'name'             => ['required', 'string', new ResourceNameRule('event')],
            'text'             => ['sometimes', 'string', 'nullable'],
            'file'             => [$fileRequired, 'array'],
            'file.file_type'   => ['required_with:file', 'string', new AllowInRule(['photo'])],
            'file.temp_file'   => ['required_with:file', 'numeric', 'exists:storage_files,id'],
            'attachments'      => ['sometimes', 'array'],
            'attachments.*.id' => ['numeric', 'exists:core_attachments,id'],
            'categories'       => ['sometimes', 'array'],
            'categories.*'     => ['numeric', new CategoryRule(resolve(CategoryRepositoryInterface::class))],
            'owner_id'         => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
            'is_online'        => ['required', 'numeric', new AllowInRule([0, 1])],
            'event_url'        => ['required_if:is_online,1', 'nullable', 'url'],
            'start_time'       => ['required', 'date', 'before:end_time'],
            'end_time'         => ['required', 'date', $this->getEventDateRule(), 'after:start_time'],
            'location'         => ['required_if:is_online,0', 'nullable', 'array'],
            'privacy'          => ['required', new PrivacyRule()],
            'host'             => ['sometimes', 'array'],
        ];

        $rules = $this->applyAttachmentRules($rules);

        return $rules;
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'event_url.required_if' => __p('event::validation.the_event_link_is_required_for_online_event'),
            'location.required_if'  => __p('event::validation.the_location_field_is_required_for_offline_event'),
            'start_time.before'     => __p('event::validation.the_event_start_time_must_be_earlier_than_end_time'),
            'end_time.after'        => __p('event::validation.the_event_end_time_must_be_greater_than_end_time'),
            'file.file_type'        => __p('event::validation.the_banner_field_must_be_the_photo'),
        ];
    }

    public function getEventDateRule(): Rule
    {
        return new DateEqualOrAfterRule(
            Carbon::now(),
            __p('event::phrase.the_event_time_should_be_greater_than_the_current_time')
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (isset($data['file']['temp_file'])) {
            $data['temp_file'] = $data['file']['temp_file'];
        }

        $data = $this->handlePrivacy($data);

        $data['location_name']      = $data['location']['address'] ?? null;
        $data['location_latitude']  = $data['location']['lat'] ?? null;
        $data['location_longitude'] = $data['location']['lng'] ?? null;
        $data['country_iso']        = $data['location']['short_name'] ?? null;
        unset($data['location']);

        if (isset($data['start_time'])) {
            $data['start_time'] = Carbon::parse($data['start_time'])->setTimezone('UTC')->toDateTimeString();
        }

        if (isset($data['end_time'])) {
            $data['end_time'] = Carbon::parse($data['end_time'])->setTimezone('UTC')->toDateTimeString();
        }

        if (array_key_exists('host', $data)) {
            $hosts        = Arr::get($data, 'host', []);
            $data['host'] = collect($hosts)->pluck('id')->toArray();
        }

        return $data;
    }
}
