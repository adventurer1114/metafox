<?php

namespace MetaFox\Marketplace\Http\Requests\v1\Listing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;
use MetaFox\Marketplace\Rules\MaximumAttachedPhotosPerUpload;
use MetaFox\Marketplace\Support\Facade\Listing;
use MetaFox\Platform\MetaFox;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\CategoryRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Rules\ResourceTextRule;
use MetaFox\Platform\Traits\Http\Request\AttachmentRequestTrait;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

class StoreRequest extends FormRequest
{
    use PrivacyRequestTrait;
    use AttachmentRequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $minLength = Listing::getMinimumTitleLength();

        $maxLength = Listing::getMaximumTitleLength();

        $context = user();

        $maxUpload = (int) $context->getPermissionValue('marketplace.maximum_number_of_attached_photos_per_upload');

        $rules = [
            'title'             => ['required', 'string', 'between:' . $minLength . ',' . $maxLength],
            'categories'        => ['required', 'array'],
            'categories.*'      => ['numeric', new CategoryRule(resolve(CategoryRepositoryInterface::class))],
            'short_description' => [
                'sometimes', 'nullable', 'string', 'between:1,' . MetaFoxConstant::DEFAULT_MAX_SHORT_DESCRIPTION_LENGTH,
            ],
            'text'     => ['sometimes', 'string', new ResourceTextRule()],
            'owner_id' => [
                'sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id'),
            ],
            'privacy'              => ['required', new PrivacyRule()],
            'location'             => ['required', 'array'],
            'location.lat'         => ['required', 'numeric'],
            'location.lng'         => ['required', 'numeric'],
            'location.address'     => ['required', 'string'],
            'location.short_name'  => ['required', 'string'],
            'attached_photos'      => ['required', 'array', new MaximumAttachedPhotosPerUpload($maxUpload)],
            'attached_photos.*.id' => [
                'required_if:attached_photos.*.status,update,remove', 'numeric',
                new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
            'attached_photos.*.status' => [
                'required_with:attached_photos', new AllowInRule([
                    MetaFoxConstant::FILE_REMOVE_STATUS, MetaFoxConstant::FILE_UPDATE_STATUS,
                    MetaFoxConstant::FILE_CREATE_STATUS, MetaFoxConstant::FILE_NEW_STATUS,
                ]),
            ],
            'attached_photos.*.temp_file' => [
                'required_if:attached_photos.*.status,create', 'numeric',
                new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
            'attached_photos.*.file_type' => [
                'required_if:attached_photos.*.status,create', 'string', new AllowInRule(
                    ['photo'],
                    __p('marketplace::phrase.the_attached_photos_are_invalid')
                ),
            ],
            'is_sold'   => ['sometimes', 'numeric', new AllowInRule([true, false])],
            'auto_sold' => ['sometimes', 'numeric', new AllowInRule([true, false])],
            'tags'      => ['sometimes', 'array'],
        ];

        $rules = $this->applyAllowPaymentRules($rules);

        $rules = $this->applyAttachmentRules($rules);

        $rules = $this->applyPriceRules($rules);

        return $rules;
    }

    /**
     * @throws AuthenticationException
     */
    protected function applyAllowPaymentRules($rules): array
    {
        $context = user();

        $rules['allow_payment'] = [
            'sometimes', new AllowInRule(
                $context->hasPermissionTo('marketplace.sell_items') ? [0, 1] : [0]
            ),
        ];

        $rules['allow_point_payment'] = [
            'sometimes', new AllowInRule(
                ($context->hasPermissionTo('marketplace.enable_activity_point_payment')
                    && $context->hasPermissionTo('marketplace.sell_items')) ? [0, 1] : [0]
            ),
        ];

        return $rules;
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'price.*.value.required' => __p('marketplace::phrase.price_is_a_required_field'),
        ];
    }

    protected function applyPriceRules(array $rules): array
    {
        if (Metafox::isMobile()) {
            $rules['price.*.value'] = ['required', 'numeric', 'gte:0'];
            $rules['price.*.label'] = ['required'];

            return $rules;
        }

        $currencies = app('currency')->getActiveOptions();
        foreach ($currencies as $currency) {
            $rules['price_' . $currency['value']] = ['required', 'numeric', 'gte:0'];
        }

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        $data = $this->handlePrivacy($data);

        if (null === Arr::get($data, 'owner_id')) {
            Arr::set($data, 'owner_id', 0);
        }

        $location = Arr::get($data, 'location');

        if (is_array($location)) {
            Arr::set($data, 'location_name', Arr::get($location, 'address'));

            Arr::set($data, 'location_latitude', Arr::get($location, 'lat'));

            Arr::set($data, 'location_longitude', Arr::get($location, 'lng'));

            Arr::set($data, 'country_iso', Arr::get($location, 'short_name'));

            unset($data['location']);
        }

        if (null === Arr::get($data, 'is_sold')) {
            Arr::set($data, 'is_sold', 0);
        }

        if (null === Arr::get($data, 'privacy')) {
            Arr::set($data, 'privacy', MetaFoxPrivacy::EVERYONE);
        }

        $data = $this->validatedAllowPayment($data);

        Arr::set($data, 'price', $this->validatedPrice($data));

        $data = $this->transformMobileFiles($data);

        return $data;
    }

    /**
     * @throws AuthenticationException
     */
    protected function validatedAllowPayment($data): array
    {
        $context = user();

        if (!$context->hasPermissionTo('marketplace.enable_activity_point_payment')
            || !$context->hasPermissionTo('marketplace.sell_items')) {
            Arr::set($data, 'allow_point_payment', 0);
        }

        if (!$context->hasPermissionTo('marketplace.sell_items')) {
            Arr::set($data, 'allow_payment', 0);
        }

        return $data;
    }

    protected function validatedPrice(array $attributes): array
    {
        $values = [];

        if (Metafox::isMobile()) {
            foreach ($attributes['price'] as $item) {
                $values[$item['label']] = round(Arr::get($item, 'value'), 2);
            }

            return $values;
        }

        $currencies = app('currency')->getActiveOptions();

        $name = 'price';

        foreach ($currencies as $currency) {
            $values[$currency['value']] = round(Arr::get($attributes, $name . '_' . $currency['value']), 2);
        }

        return $values;
    }

    protected function transformMobileFiles(array $attributes): array
    {
        if (!count($attributes)) {
            return $attributes;
        }

        $photos = Arr::get($attributes, 'attached_photos');

        if (!is_array($photos)) {
            return $attributes;
        }

        $photos = array_map(function ($photo) {
            if (Arr::get($photo, 'status') != MetaFoxConstant::FILE_NEW_STATUS) {
                return $photo;
            }

            Arr::set($photo, 'status', MetaFoxConstant::FILE_CREATE_STATUS);

            return $photo;
        }, $photos);

        Arr::set($attributes, 'attached_photos', $photos);

        return $attributes;
    }
}
