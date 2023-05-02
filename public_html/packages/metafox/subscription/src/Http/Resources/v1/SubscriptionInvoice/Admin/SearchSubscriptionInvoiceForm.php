<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Subscription\Models\SubscriptionPackage as Model;
use MetaFox\Subscription\Repositories\SubscriptionPackageRepositoryInterface;
use MetaFox\Subscription\Support\Helper;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchSubscriptionPackageForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class SearchSubscriptionInvoiceForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('subscription::admin.manage_subscriptions'))
            ->noHeader()
            ->action('/admincp/subscription-invoice')
            ->acceptPageParams(['member_name', 'id', 'package_id', 'payment_status'])
            ->submitAction('@formAdmin/search/SUBMIT');
    }

    public function initialize(): void
    {
        $this->addBasic()
            ->asHorizontal()
            ->addFields(
                Builder::text('member_name')
                    ->forAdminSearchForm()
                    ->optional()
                    ->label(__p('subscription::admin.member_name')),
                Builder::text('id')
                    ->forAdminSearchForm()
                    ->optional()
                    ->label(__p('subscription::admin.invoice_id'))
                    ->yup(
                        Yup::number()
                            ->nullable()
                            ->setError('typeError', __p('subscription::admin.order_id_must_be_numeric'))
                    ),
                Builder::choice('package_id')
                    ->forAdminSearchForm()
                    ->optional()
                    ->options($this->getPackageOptions())
                    ->label(__p('subscription::admin.package_title')),
                Builder::choice('payment_status')
                    ->forAdminSearchForm()
                    ->optional()
                    ->options($this->getStatusOptions())
                    ->label(__p('subscription::admin.payment_status')),
                Builder::submit()
                    ->forAdminSearchForm(),
            );
    }

    protected function getStatusOptions(): array
    {
        return [
            [
                'label' => __p('subscription::phrase.payment_status.active'),
                'value' => Helper::getCompletedPaymentStatus(),
            ],
            [
                'label' => __p('subscription::phrase.payment_status.cancelled'),
                'value' => Helper::getCanceledPaymentStatus(),
            ],
            [
                'label' => __p('subscription::phrase.payment_status.expired'),
                'value' => Helper::getExpiredPaymentStatus(),
            ],
            [
                'label' => __p('subscription::phrase.payment_status.pending_payment'),
                'value' => Helper::getPendingPaymentStatus(),
            ],
        ];
    }

    protected function getPackageOptions(): array
    {
        $packages = resolve(SubscriptionPackageRepositoryInterface::class)->getActivePackages();

        $options = [];

        if ($packages->count()) {
            foreach ($packages as $package) {
                $options[] = [
                    'label' => $package->toTitle(),
                    'value' => $package->entityId(),
                ];
            }
        }

        return $options;
    }
}
