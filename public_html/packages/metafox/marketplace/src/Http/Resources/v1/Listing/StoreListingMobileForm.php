<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\PrivacyFieldMobileTrait;
use MetaFox\Form\Section;
use MetaFox\Marketplace\Http\Requests\v1\Listing\CreateFormRequest;
use MetaFox\Marketplace\Models\Category;
use MetaFox\Marketplace\Models\Listing as Model;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;
use MetaFox\Marketplace\Support\Facade\Listing;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreListingForm.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreListingMobileForm extends AbstractForm
{
    use PrivacyFieldMobileTrait;

    protected function prepare(): void
    {
        $categoryRepository = resolve(CategoryRepositoryInterface::class);
        $values             = [
            'privacy'             => $this->getPrivacy(),
            'owner_id'            => $this->getOwnerId(),
            'attachments'         => [],
            'attached_photos'     => [],
            'location'            => null,
            'allow_payment'       => 0,
            'allow_point_payment' => 0,
            'is_sold'             => 0,
            'auto_sold'           => 1,
            'is_moderator'        => $this->isModerator(),
        ];

        $values          = $this->preparePrices($values);
        $categoryDefault = $categoryRepository->getCategoryDefault();

        if ($categoryDefault?->is_active == Category::IS_ACTIVE) {
            Arr::set($values, 'categories', [
                $categoryDefault->entityId(),
            ]);
        }

        $this->title(__p('marketplace::phrase.add_new_listing'))
            ->action('marketplace')
            ->asPost()
            ->setValue($values);
    }

    protected function preparePrices(array $values): array
    {
        $currencies = app('currency')->getActiveOptions();

        if (!is_array($currencies) || count($currencies) === 0) {
            return $values;
        }

        $name = 'price_';

        $data = [];

        foreach ($currencies as $currency) {
            $value = MetaFoxConstant::EMPTY_STRING;

            $data[] = [
                'name'  => $name . $currency['value'],
                'label' => $currency['value'],
                'value' => $value,
            ];
        }

        Arr::set($values, 'price', $data);

        return $values;
    }

    protected function isModerator(): bool
    {
        return false;
    }

    protected function getPrivacy(): int
    {
        $context = user();

        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'marketplace.item_privacy');

        if (false !== $privacy) {
            return $privacy;
        }

        return MetaFoxPrivacy::EVERYONE;
    }

    protected function getOwnerId(): int
    {
        if (null !== $this->resource) {
            return $this->resource->ownerId();
        }

        if (null !== $this->owner) {
            return $this->owner->entityId();
        }

        return 0;
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic          = $this->addBasic();
        $minTitleLength = Listing::getMinimumTitleLength();
        $maxTitleLength = Listing::getMaximumTitleLength();

        $basic->addFields(
            Builder::text('title')
                ->required()
                ->marginNormal()
                ->label(__p('marketplace::phrase.what_are_you_selling'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxTitleLength]))
                ->placeholder(__p('marketplace::phrase.listing_title'))
                ->maxLength($maxTitleLength)
                ->yup(
                    Yup::string()
                        ->required(__p('core::phrase.title_is_a_required_field'))
                        ->minLength(
                            $minTitleLength,
                            __p('core::validation.title_minimum_length_of_characters', [
                                'number' => $minTitleLength,
                            ])
                        )
                ),
            Builder::textArea('short_description')
                ->maxLength(MetaFoxConstant::DEFAULT_MAX_SHORT_DESCRIPTION_LENGTH)
                ->label(__p('marketplace::phrase.short_description'))
                ->placeholder(__p('marketplace::phrase.type_something_dot')),
            Builder::richTextEditor('text')
                ->required()
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('marketplace::phrase.type_something_dot'))
                ->yup(
                    Yup::string()
                        ->required()
                ),
        );

        $this->addPriceFields($basic);

        $basic->addFields(
            Builder::category('categories')
                ->label(__p('core::phrase.category'))
                ->multiple(false)
                ->required()
                ->setRepository(CategoryRepositoryInterface::class)
                ->yup(
                    Yup::number()
                        ->required(__p('marketplace::phrase.category_is_a_required_field'))
                ),
            Builder::location('location')
                ->required()
                ->placeholder(__p('marketplace::phrase.enter_location'))
                ->yup(
                    Yup::object()
                        ->nullable()
                        ->required(__p('marketplace::phrase.location_is_a_required_field'))
                        ->setError('required', __p('marketplace::phrase.location_is_a_required_field'))
                        ->setError('typeError', __p('marketplace::phrase.location_is_a_required_field'))
                        ->addProperty(
                            'lat',
                            Yup::number()
                                ->nullable()
                        )
                        ->addProperty(
                            'lng',
                            Yup::number()
                                ->nullable()
                        )
                        ->addProperty(
                            'address',
                            Yup::string()
                                ->nullable()
                        )
                ),
        );

        $this->addAttachedPhotosField($basic);

        $basic->addFields(
            $this->buildAllowPaymentField(),
            $this->buildAllowPointPaymentField(),
            $this->buildAutoSoldField(),
            Builder::hidden('owner_id'),
        );

        if ($this->isEdit()) {
            if ($this->resource->is_approved) {
                $basic->addFields(
                    Builder::switch('is_sold')
                        ->label(__p('marketplace::phrase.closed_item_sold'))
                        ->description(__p('marketplace::phrase.enable_close_option_listing_closed'))
                );
            }
        }

        $basic->addFields(
            Builder::tags()
                ->marginNormal()
                ->label(__p('marketplace::phrase.product_tags'))
                ->placeholder(__p('core::phrase.keywords')),
            $this->buildPrivacyField()
                ->description(__p('marketplace::phrase.control_who_can_see_this_listing')),
        );
    }

    /**
     * @throws AuthenticationException
     */
    protected function buildAllowPaymentField(): ?AbstractField
    {
        $context = user();

        $paymentSettingUrl = null;

        if (method_exists($context, 'toPaymentSettingUrl')) {
            $paymentSettingUrl = call_user_func([$context, 'toPaymentSettingUrl']);
        }

        if (!$context->hasPermissionTo('marketplace.sell_items')) {
            return null;
        }

        return Builder::switch('allow_payment')
            ->label(__p('marketplace::phrase.enable_instant_payment'))
            ->description(__p('marketplace::phrase.enable_instant_payment_description', [
                'hasLink' => $paymentSettingUrl ? 1 : 0,
                'link'    => $paymentSettingUrl ?: '',
            ]));
    }

    protected function buildAutoSoldField(): ?AbstractField
    {
        $context = user();

        if (!$context->hasPermissionTo('marketplace.sell_items')) {
            return null;
        }

        return Builder::switch('auto_sold')
            ->label(__p('marketplace::phrase.auto_sold'))
            ->description(__p('marketplace::phrase.auto_sold_description'));
    }

    /**
     * @throws AuthenticationException
     */
    protected function buildAllowPointPaymentField(): ?AbstractField
    {
        $context = user();

        if (!$context->hasPermissionTo('marketplace.enable_activity_point_payment')
            || !$context->hasPermissionTo('marketplace.sell_items')) {
            return null;
        }

        return Builder::switch('allow_point_payment')
            ->label(__p('marketplace::phrase.enable_point_payment'))
            ->description(__p('marketplace::phrase.enable_point_payment_description'));
    }

    protected function addAttachedPhotosField(Section $basic): void
    {
        $context = user();

        $maxUpload = (int) $context->getPermissionValue('marketplace.maximum_number_of_attached_photos_per_upload');

        $fileSize = file_type()->getFilesizeInMegabytes('photo');

        $field = Builder::multiFile('attached_photos')
            ->label(__p('marketplace::phrase.attached_photos'))
            ->description(__p('marketplace::phrase.upload_attached_photos_description', [
                'max_file_size'        => $fileSize,
                'has_limit_per_upload' => $maxUpload > 0 ? 1 : 0,
                'max_per_upload'       => $maxUpload,
            ]))
            ->maxFilesDescription(__p('marketplace::phrase.you_have_reached_thue_limit_file', [
                'max_per_upload' => $maxUpload,
            ]))
            ->accept('image/*')
            ->itemType('marketplace')
            ->uploadUrl('file')
            ->required()
            ->maxFiles($maxUpload);

        $yup = Yup::array()->min(1);
        /*
         * In case value is 0, it means unlimit
         */
        if ($maxUpload > 0) {
            $yup->maxWhen([
                'value' => $maxUpload,
                'when'  => [
                    'includes', 'item.status',
                    [MetaFoxConstant::FILE_CREATE_STATUS, MetaFoxConstant::FILE_UPDATE_STATUS],
                ],
            ], __p('marketplace::phrase.maximum_per_upload_limit_reached', ['limit' => $maxUpload]))
                ->of(
                    Yup::object()
                        ->addProperty('id', Yup::number())
                        ->addProperty('type', Yup::string())
                        ->addProperty('status', Yup::string())
                );
        }

        $field->yup($yup);

        $basic->addField($field);
    }

    protected function addPriceFields(Section $basic): void
    {
        $description = __p('marketplace::phrase.amount_you_want_to_sell');

        $basic->addField(
            Builder::price('price')
                ->yup(
                    Yup::array()
                        ->of(
                            Yup::object()
                                ->addProperty(
                                    'value',
                                    Yup::number()
                                        ->required(__p('marketplace::phrase.price_is_a_required_field'))
                                        ->min(
                                            0,
                                            __p(
                                                'marketplace::phrase.price_must_be_greater_than_or_equal_to_number',
                                                ['number' => 0]
                                            )
                                        )
                                        ->setError('typeError', __p('marketplace::phrase.price_must_be_number'))
                                )
                        )
                )
                ->description($description)
                ->findReplace([
                    'find'    => [','],
                    'replace' => '.',
                ])
        );
    }

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function boot(CreateFormRequest $request, ?int $id = null): void
    {
        $data = $request->validated();

        $ownerId = Arr::get($data, 'owner_id');

        $context = user();

        $this->setOwner($context);

        if ($ownerId > 0) {
            $owner = UserEntity::getById($ownerId)->detail;
            $this->setOwner($owner);
        }

        policy_authorize(ListingPolicy::class, 'create', $context, $this->owner);
    }

    protected function isEdit(): bool
    {
        return false;
    }
}
