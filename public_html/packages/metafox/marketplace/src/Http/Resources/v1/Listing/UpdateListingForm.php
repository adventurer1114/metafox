<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Marketplace\Http\Requests\v1\Listing\CreateFormRequest;
use MetaFox\Marketplace\Models\Listing as Model;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Marketplace\Repositories\ListingRepositoryInterface;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateListingForm.
 * @property Model $resource
 */
class UpdateListingForm extends StoreListingForm
{
    protected function prepare(): void
    {
        $values = [
            'title'         => $this->resource->title,
            'location'      => $this->resource->toLocationObject(),
            'owner_id'      => $this->resource->owner_id,
            'attachments'   => $this->resource->attachmentsForForm(),
            'tags'          => $this->resource->tags ?? [],
            'allow_payment' => (int) $this->resource->allow_payment,
            'auto_sold'     => (int) $this->resource->auto_sold,
            'is_moderator'  => $this->isModerator(),
        ];

        if ($this->resource->is_approved) {
            Arr::set($values, 'is_sold', (int) $this->resource->is_sold);
        }

        $values = $this->preparePrivacy($values);

        $values = $this->prepareAllowPointPayment($values);

        $values = $this->prepareCategories($values);

        $values = $this->prepareText($values);

        $values = $this->preparePrices($values);

        $values = $this->prepareAttachedPhotos($values);

        $this
            ->title(__('marketplace::phrase.edit_listing'))
            ->action(url_utility()->makeApiUrl("marketplace/{$this->resource->entityId()}"))
            ->setBackProps(__p('marketplace::phrase.marketplace'))
            ->asPut()
            ->setValue($values);
    }

    protected function isModerator(): bool
    {
        $context = user();

        $owner = $this->resource->owner;

        if (null === $owner) {
            return false;
        }

        if ($owner->entityId() == $context->entityId()) {
            return true;
        }

        if ($owner instanceof HasPrivacyMember) {
            return false;
        }

        if ($context->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        return false;
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepareAllowPointPayment(array $values): array
    {
        if (user()->hasPermissionTo('marketplace.enable_activity_point_payment')
            && user()->hasPermissionTo('marketplace.sell_items')) {
            Arr::set($values, 'allow_point_payment', (int) $this->resource->allow_point_payment);
        }

        return $values;
    }

    protected function prepareCategories(array $values): array
    {
        $categories = $this->resource->categories
            ->pluck('id')
            ->toArray();

        Arr::set($values, 'categories', $categories);

        return $values;
    }

    protected function prepareText(array $values): array
    {
        $text = $miniDescription = MetaFoxConstant::EMPTY_STRING;

        if ($this->resource->short_description) {
            $miniDescription = $this->resource->short_description;
        }

        if (null !== $this->resource->marketplaceText) {
            $text = $this->resource->marketplaceText->text_parsed;
        }

        Arr::set($values, 'short_description', $miniDescription);

        Arr::set($values, 'text', $text);

        return $values;
    }

    protected function preparePrivacy(array $values): array
    {
        $privacy = $this->resource->privacy;

        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $privacy = array_column($lists, 'item_id');
        }

        Arr::set($values, 'privacy', $privacy);

        return $values;
    }

    protected function preparePrices(array $values): array
    {
        $currencies = app('currency')->getActiveOptions();

        $prices = $this->resource->price;

        $name = 'price_';

        foreach ($currencies as $currency) {
            $value = MetaFoxConstant::EMPTY_STRING;

            if (Arr::has($prices, $currency['value'])) {
                $value = Arr::get($prices, $currency['value']);
            }

            Arr::set($values, $name . $currency['value'], $value);
        }

        return $values;
    }

    protected function prepareAttachedPhotos(array $values): array
    {
        $items = [];

        if ($this->resource->photos->count()) {
            $items = $this->resource->photos->map(function ($photo) {
                return ResourceGate::asItem($photo, null);
            });
        }

        Arr::set($values, 'attached_photos', $items);

        return $values;
    }

    protected function isEdit(): bool
    {
        return true;
    }

    public function boot(CreateFormRequest $request, ?int $id = null)
    {
        $context = user();

        $this->resource = resolve(ListingRepositoryInterface::class)->find($id);

        $this->setOwner($this->resource->owner);

        policy_authorize(ListingPolicy::class, 'update', $context, $this->resource);
    }
}
