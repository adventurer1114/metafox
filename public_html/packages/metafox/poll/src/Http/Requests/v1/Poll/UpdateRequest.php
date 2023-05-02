<?php

namespace MetaFox\Poll\Http\Requests\v1\Poll;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MetaFox\Core\Rules\DateEqualOrAfterRule;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Rules\ResourceNameRule;
use MetaFox\Platform\Rules\UniqueValueInArray;
use MetaFox\Platform\Traits\Http\Request\AttachmentRequestTrait;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;
use MetaFox\Poll\Models\Answer;
use MetaFox\Poll\Rules\UpdateBannerRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Poll\Http\Controllers\Api\v1\PollController::update;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    use PrivacyRequestTrait;
    use AttachmentRequestTrait;

    /**
     * @var UpdateBannerRule
     */
    private $bannerRule;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $minAnswerLength = StoreRequest::MIN_ANSWER_LENGTH;
        $maxAnswerLength = StoreRequest::MAX_ANSWER_LENGTH;

        $rules = [
            'question'         => ['sometimes', 'string', new ResourceNameRule('poll')],
            'answers'          => ['sometimes', 'array', 'min:2', new UniqueValueInArray(['answer'])],
            'answers.*.id'     => ['sometimes', 'numeric', 'exists:' . Answer::class . ',id'],
            'answers.*.answer' => ['required_with:answers', 'string', "between:{$minAnswerLength},{$maxAnswerLength}"],
            'item_id'          => ['sometimes', 'numeric', 'exists:user_entities,id'],
            'file'             => $this->buildBannerRule(),
            'text'             => ['sometimes', 'string', 'nullable'],
            'background'       => ['sometimes', 'string', 'size:6'],
            'percentage'       => ['sometimes', 'string', 'size:6'],
            'border'           => ['sometimes', 'string', 'size:6'],
            'enable_close'     => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'close_time'       => ['required_if:enable_close,1', 'date', 'nullable', $this->getCloseTimeRule()],
            'is_multiple'      => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'public_vote'      => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'randomize'        => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'privacy'          => ['sometimes', new PrivacyRule()],
            'has_banner'       => ['sometimes', 'numeric', new AllowInRule([0, 1])],
        ];

        $rules = $this->applyAttachmentRules($rules);

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = $this->handlePrivacy($data);

        if (isset($data['answers'])) {
            $ordering = 0;

            $formattedAnswer                   = [];
            $formattedAnswer['changedAnswers'] = [];

            foreach ($data['answers'] as $answer) {
                // Reset the ordering
                $answer['ordering'] = ++$ordering;

                if (isset($answer['id'])) {
                    $formattedAnswer['changedAnswers'][$answer['id']] = $answer;
                    continue;
                }

                $formattedAnswer['newAnswers'][] = $answer;
            }

            $data['answers'] = $formattedAnswer;
        }

        if (isset($data['close_time'])) {
            $data['closed_at'] = $data['close_time'];
            unset($data['close_time']);
        }

        // This check is supposed verify case disable close but still send close_time to API
        if (empty($data['enable_close'])) {
            $data['closed_at'] = null;
        }

        if (isset($data['file']['temp_file'])) {
            $data['temp_file'] = $data['file']['temp_file'];
        }

        if (isset($data['file']['status'])) {
            $data['remove_image'] = true;
        }

        if (isset($data['item_id'])) {
            $data['owner_id'] = $data['item_id'];
        }

        if (Arr::get($data, 'file.status', '') == 'keep') {
            unset($data['file']);
        }

        return $data;
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'answers.*.answer.required_with' => __p('poll::phrase.answer_is_a_required_field'),
            'answers.*.answer.between'       => __p('validation.between.string', [
                'attribute' => 'answer',
                'min'       => StoreRequest::MIN_ANSWER_LENGTH,
                'max'       => StoreRequest::MAX_ANSWER_LENGTH,
            ]),
            'close_time.required_if' => __p('validation.required'),
            'file.required_if'       => __p('validation.field_is_a_required_field', ['field' => 'Banner']),
        ];
    }

    public function getBannerRule(): UpdateBannerRule
    {
        if (!$this->bannerRule instanceof UpdateBannerRule) {
            $this->bannerRule = resolve(UpdateBannerRule::class);
        }

        return $this->bannerRule;
    }

    public function setBannerRule(UpdateBannerRule $rule): void
    {
        $this->bannerRule = $rule;
    }

    /**
     * @return array<int, mixed>
     */
    private function buildBannerRule(): array
    {
        $bannerRule = $this->getBannerRule();

        if ($bannerRule->isImageRequired()) {
            return ['required_if:has_banner,0', 'array', $bannerRule];
        }

        return ['sometimes', 'nullable', 'array', $bannerRule];
    }

    public function getCloseTimeRule(): Rule
    {
        return new DateEqualOrAfterRule(
            Carbon::now(),
            __p('poll::phrase.the_close_time_should_be_greater_than_the_current_time')
        );
    }
}
