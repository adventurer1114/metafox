<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\Admin;

use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\BatchActionMenu;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;
use MetaFox\Subscription\Support\Helper;

class DataGrid extends Grid
{
    protected string $appName      = 'subscription';
    protected string $resourceName = 'invoice';

    protected function initialize(): void
    {
        $this->setDataSource(apiUrl('admin.subscription.invoice.index'), [], [
            'member_name' => [
                'truthy',
                'member_name',
            ],
            'id' => [
                'truthy',
                'id',
            ],
            'package_title' => [
                'truthy',
                'package_title',
            ],
            'payment_status' => [
                'includes',
                'payment_status',
                Helper::getPaymentStatusForSearching(),
            ],
        ]);

        $this->addColumn('id')
            ->header(__p('subscription::admin.invoice_id'))
            ->width(100)
            ->flex()
            ->linkTo('detail');

        $this->addColumn('user.full_name')
            ->header(__p('subscription::admin.member_name'))
            ->width(200)
            ->flex()
            ->linkTo('user.url');

        $this->addColumn('package.title')
            ->header(__p('subscription::admin.package_title'))
            ->width(300)
            ->flex();

        $this->addColumn('payment_status')
            ->header(__p('subscription::admin.status'))
            ->width(200)
            ->flex();

        $this->addColumn('activated_at')
            ->header(__p('subscription::admin.activation_date'))
            ->asDateTime()
            ->width(200)
            ->flex();

        $this->addColumn('expired_at')
            ->header(__p('subscription::admin.expiration_date'))
            ->width(200)
            ->asDateTime()
            ->flex();

        $this->withBatchMenu(function (BatchActionMenu $menu) {
        });

        $this->withActions(function (Actions $actions) {
            $actions->add('activeItem')
                ->apiUrl('/admincp/subscription/invoice/active/:id')
                ->asPatch()
                ->confirm([
                    'title'   => __p('subscription::admin.activate_subscription'),
                    'message' => __p('subscription::admin.are_you_sure_you_want_to_activate_this_subscription'),
                ]);

            $actions->add('cancelItem')
                ->apiUrl('/admincp/subscription/invoice/cancel/:id')
                ->asPatch()
                ->confirm([
                    'title'   => __p('subscription::admin.cancel_subscription'),
                    'message' => __p('subscription::admin.are_you_sure_you_want_to_cancel_this_subscription'),
                ]);

            $actions->add('viewReason')
                ->apiUrl('/admincp/subscription/invoice/user-reason/:id');
        });

        $this->withItemMenu(function (ItemActionMenu $itemActionMenu) {
            $itemActionMenu->addItem('viewTransaction')
                ->label(__p('subscription::admin.view_transactions'))
                ->icon('ico-barchart-o')
                ->value(MetaFoxForm::ACTION_ROW_LINK)
                ->params([
                    'to' => '/admincp/subscription/invoice/detail/:id',
                ]);

            $itemActionMenu->addItem('viewReason')
                ->label(__p('subscription::admin.view_reason'))
                ->icon('ico-info-circle-alt-o')
                ->value(MetaFoxForm::ACTION_ROW_EDIT)
                ->params([
                    'action' => 'viewReason',
                ])
                ->showWhen([
                    'truthy',
                    'item.extra.can_view_reason',
                ]);

            $itemActionMenu->addItem('activeItem')
                ->label(__p('subscription::admin.activate_subscription'))
                ->icon('ico-unlock-o')
                ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
                ->params([
                    'action' => 'activeItem',
                ])
                ->showWhen([
                    'truthy',
                    'item.extra.can_activate',
                ]);

            $itemActionMenu->addItem('cancelItem')
                ->label(__p('subscription::admin.cancel_subscription'))
                ->icon('ico-lock-o')
                ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
                ->params([
                    'action' => 'cancelItem',
                ])
                ->showWhen([
                    'truthy',
                    'item.extra.can_cancel',
                ]);
        });
    }
}
