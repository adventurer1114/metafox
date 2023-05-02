<?php

namespace MetaFox\Activity\Http\Resources\v1\Post;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use MetaFox\Activity\Models\Post;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\MetaFoxConstant;

class CreateFeedForm extends AbstractForm
{
    /**
     * @var bool
     */
    protected $isEdit;

    /**
     * @param      $resource
     * @param bool $isEdit
     */
    public function __construct($resource = null, bool $isEdit = false)
    {
        parent::__construct($resource);

        $this->isEdit = $isEdit;
    }

    /**
     * @param  Request                                    $request
     * @return array|null
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request): array
    {
        $data = $request->all();

        $rules = $this->getValidationRules($data);

        $transformedData = $data;

        if (count($rules)) {
            $messages = $this->getValidationMessages();

            $validation = Validator::make($data, $rules, $messages);

            $transformedData = $validation->validated();
        }

        return $this->transformData($transformedData);
    }

    /**
     * @param  array $data
     * @return array
     */
    protected function getValidationRules(array $data): array
    {
        $location = Arr::get($data, 'location');

        $tagFriends = Arr::get($data, 'tagged_friends', []);

        if (null !== $location || !empty($tagFriends)) {
            return [];
        }

        return [
            'user_status' => ['required_if:post_type,' . Post::FEED_POST_TYPE],
        ];
    }

    /**
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [
            'user_status.required_if' => __p('activity::validation.add_some_text_to_share'),
        ];
    }

    protected function transformData(array $data): array
    {
        $status = Arr::get($data, 'user_status');

        if (null === $status) {
            $status = MetaFoxConstant::EMPTY_STRING;
        }

        if (!Arr::has($data, 'content')) {
            Arr::set($data, 'content', trim($status));
        }

        return $data;
    }
}
