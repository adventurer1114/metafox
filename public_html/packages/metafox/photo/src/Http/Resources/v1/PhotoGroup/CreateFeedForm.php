<?php

namespace MetaFox\Photo\Http\Resources\v1\PhotoGroup;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use MetaFox\Form\AbstractForm;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Rules\MaximumMediaPerUpload;
use MetaFox\Photo\Support\Facades\Photo as Facade;
use MetaFox\Platform\Rules\AllowInRule;

class CreateFeedForm extends AbstractForm
{
    /**
     * @var bool
     */
    protected $isEdit;

    public function __construct($resource = null, bool $isEdit = false)
    {
        parent::__construct($resource);

        $this->isEdit = $isEdit;
    }

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
     * @throws AuthenticationException
     */
    protected function getValidationRules(): array
    {
        $context      = user();
        $maxPerUpload = $context->getPermissionValue('photo.maximum_number_of_media_per_upload');

        if ($this->isEdit) {
            return [
                'photo_files'                  => ['sometimes', 'array', 'nullable', new MaximumMediaPerUpload($maxPerUpload)],
                'photo_files.*.id'             => ['required_with:photo_files', 'numeric'],
                'photo_files.*.type'           => ['required_with:photo_files', 'string'],
                'photo_files.*.status'         => ['required_with:photo_files', 'string', new AllowInRule(['new', 'remove', 'edit'])],
                'photo_files.*.base64'         => ['sometimes', 'string'],
                'photo_files.*.tagged_friends' => ['sometimes', 'array'],
                'photo_files.*.text'           => ['sometimes', 'nullable', 'string'],
                'photo_description'            => ['sometimes'],
            ];
        }

        return [
            'photo_files'                  => ['required_if:post_type,' . PhotoGroup::FEED_POST_TYPE, 'array', new MaximumMediaPerUpload($maxPerUpload)],
            'photo_files.*.id'             => ['required_with:photo_files', 'numeric'],
            'photo_files.*.type'           => ['required_with:photo_files', 'string'],
            'photo_files.*.status'         => ['required_with:photo_files', 'string', new AllowInRule(['new', 'remove'])],
            'photo_files.*.tagged_friends' => ['sometimes', 'array'],
            'photo_files.*.text'           => ['sometimes', 'nullable', 'string'],
            'photo_description'            => ['sometimes'],
        ];
    }

    /**
     * @param  array $data
     * @return array
     */
    protected function transformData(array $data): array
    {
        return Facade::transformDataForFeed($data);
    }
}
