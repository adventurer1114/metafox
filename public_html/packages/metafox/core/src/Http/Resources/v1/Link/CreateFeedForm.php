<?php

namespace MetaFox\Core\Http\Resources\v1\Link;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use MetaFox\Core\Models\Link;
use MetaFox\Form\AbstractForm;

class CreateFeedForm extends AbstractForm
{
    /**
     * @param  Request                                    $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request): array
    {
        $data = $request->all();

        $rules = $this->getValidationRules();

        $validator = Validator::make($data, $rules);

        $data = array_merge($validator->validated(), [
            'user_status' => Arr::get($data, 'user_status', ''),
        ]);

        return $this->transformData($data);
    }

    /**
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'link_image'       => ['required_if:post_type,' . Link::FEED_POST_TYPE . '|max:10240|mimes:png,jpg'],
            'link_url'         => ['sometimes'],
            'link_description' => ['sometimes'],
            'link_title'       => ['sometimes'],
            'link_embed_code'  => ['sometimes'],
        ];
    }

    protected function transformData(array $data): array
    {
        if (!Arr::has($data, 'content')) {
            Arr::set($data, 'content', Arr::get($data, 'user_status', ''));
        }

        return $data;
    }
}
