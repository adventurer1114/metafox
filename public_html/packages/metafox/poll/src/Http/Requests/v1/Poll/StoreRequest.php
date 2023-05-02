<?php

namespace MetaFox\Poll\Http\Requests\v1\Poll;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Core\Rules\DateEqualOrAfterRule;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Rules\ResourceNameRule;
use MetaFox\Platform\Rules\UniqueValueInArray;
use MetaFox\Platform\Traits\Http\Request\AttachmentRequestTrait;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Poll\Http\Controllers\Api\v1\PollController::store;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
{
    use PrivacyRequestTrait;
    use AttachmentRequestTrait;

    public const MIN_ANSWER_LENGTH = 1;
    public const MAX_ANSWER_LENGTH = 50;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $minAnswerLength = self::MIN_ANSWER_LENGTH;
        $maxAnswerLength = self::MAX_ANSWER_LENGTH;

        $rules = [
            'question'         => ['required', 'string', new ResourceNameRule('poll')],
            'answers'          => ['required', 'array', 'min:2'],
            'answers.*.answer' => ['required', 'string', "between:{$minAnswerLength},{$maxAnswerLength}"],
            'owner_id'         => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
            'text'             => ['sometimes', 'string', 'nullable'],
            'background'       => ['sometimes', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'percentage'       => ['sometimes', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'border'           => ['sometimes', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'enable_close'     => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'close_time'       => ['required_if:enable_close,1', 'date', 'nullable', $this->getCloseTimeRule()],
            'is_multiple'      => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'public_vote'      => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'randomize'        => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'privacy'          => ['required', new PrivacyRule()],
        ];

        $this->handleRequiredImage($rules);

        $rules = $this->applyAttachmentRules($rules);

        return $rules;
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'question.required' => __p('validation.field_is_a_required_field', [
                'field' => 'Question',
            ]),
            'answers.*.answer.required' => __p('poll::phrase.answer_is_a_required_field'),
            'answers.*.answer.between'  => __p('validation.between.string', [
                'attribute' => 'answer',
                'min'       => self::MIN_ANSWER_LENGTH,
                'max'       => self::MAX_ANSWER_LENGTH,
            ]),
            'close_time.required_if' => __p('validation.required'),
            'file.required'          => __p('validation.field_is_a_required_field', ['field' => 'Photo']),
        ];
    }

    /**
     * @throws ValidationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        //Validate if answer list is unique
        $rule = ['answers' => new UniqueValueInArray(['answer'])];
        Validator::make($data, $rule)->validate();

        $data = $this->handlePrivacy($data);

        if (isset($data['answers'])) {
            $ordering = 0;
            foreach ($data['answers'] as $key => $answer) {
                $answer['ordering']    = ++$ordering;
                $data['answers'][$key] = $answer;
            }
        }

        if (isset($data['file']['temp_file'])) {
            $data['temp_file'] = $data['file']['temp_file'];
        }

        if (empty($data['owner_id'])) {
            $data['owner_id'] = 0;
        }

        if (isset($data['close_time'])) {
            $data['closed_at'] = $data['close_time'];
            unset($data['close_time']);
        }

        // This check is suppose verify case disable close but still send close_time to API
        if (empty($data['enable_close'])) {
            unset($data['closed_at']);
        }

        if (!isset($data['background'])) {
            $data['background'] = '#ebebeb';
        }

        if (!isset($data['percentage'])) {
            $data['percentage'] = '#297fc7';
        }

        if (!isset($data['text'])) {
            $data['text'] = '';
        }

        return $data;
    }

    public function getCloseTimeRule(): Rule
    {
        return new DateEqualOrAfterRule(
            Carbon::now(),
            __p('poll::phrase.the_close_time_should_be_greater_than_the_current_time')
        );
    }

    protected function handleRequiredImage(array &$rules): void
    {
        $user = user();

        if (!$user->hasPermissionTo('poll.upload_image')) {
            return;
        }

        $isRequiredImage   = Settings::get('poll.is_image_required', false);
        $requireImageField = $isRequiredImage ? 'required' : 'sometimes';

        $rules['file']           = [$requireImageField, 'array'];
        $rules['file.temp_file'] = ['required_with:file', 'numeric', 'exists:storage_files,id'];
    }
}
