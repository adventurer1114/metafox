<?php

namespace MetaFox\User\Http\Resources\v1\User\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\BatchActionMenu;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName      = 'user';
    protected string $resourceName = 'user';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function initialize(): void
    {
        $this->setSearchForm(new SearchUserForm());

        $this->searchFormPlacement('header');

        $this->setDataSource(apiUrl('admin.user.index'), [
            'q'                => ':q',
            'email'            => ':email',
            'group'            => ':group',
            'status'           => ':status',
            'gender'           => ':gender',
            'postal_code'      => ':postal_code',
            'country_state_id' => ':country_state_id',
            'country'          => ':country',
            'age_from'         => ':age_from',
            'age_to'           => ':age_to',
            'sort'             => ':sort',
            'ip_address'       => ':ip_address',
        ]);

        $this->enableCheckboxSelection();

        $this->addColumn('id')
            ->header('ID')
            ->width(80);

        $this->addColumn('user')
            ->header(__p('core::web.photo'))
            ->renderAs('AvatarCell')
            ->width(120);

        $this->addColumn('full_name')
            ->header(__p('core::phrase.user_name'))
            ->linkTo('user_link')
            ->width(200);

        $this->addColumn('email')
            ->header(__p('core::phrase.email_address'))
            ->asEmail('email')
            ->flex();

        $this->addColumn('role_name')
            ->header(__p('core::web.groups'))
            ->width(200)
            ->flex();

        $this->addColumn('last_activity')
            ->header(__p('user::phrase.last_activity'))
            ->sortable()
            ->flex();

        $this->addColumn('ip_address')
            ->header(__p('user::phrase.ip_address'))
            ->sortable()
            ->flex();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['destroy']);

            $actions->addEditPageUrl();

            $this->actionApproveUser($actions);
            $this->actionDeniedUser($actions);
            $this->actionResendVerificationEmail($actions);
            $this->actionRemoveAuthenticator($actions);
            $this->actionFeatureUser($actions);
            $this->actionUnFeatureUser($actions);
            $this->actionBanItem($actions);
            $this->actionUnBanItem($actions);
            $this->actionMoveRole($actions);
            $this->actionBatchApprove($actions);
            $this->actionBan($actions);
            $this->actionUnBan($actions);
            $this->actionDelete($actions);
            $this->actionBatchVerify($actions);
            $this->actionVerifyUser($actions);
            $this->actionBatchResendVerificationEmail($actions);
        });

        /*
         * with batch menu actions
         */
        $this->withBatchMenu(function (BatchActionMenu $menu) {
            $menu->asButton();
            $this->batchMoveRole($menu);
            $this->batchApprove($menu);
            $this->batchBan($menu);
            $this->batchUnBan($menu);
            $this->batchDelete($menu);
            $this->batchVerify($menu);
            $this->batchResendVerificationEmail($menu);
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit()->showWhen(['truthy', 'item.extra.can_edit']);
            $menu->withDelete()->showWhen(['truthy', 'item.extra.can_edit']);
            $this->banItem($menu);
            $this->unBanItem($menu);
            $this->featureUser($menu);
            $this->unFeatureUser($menu);
            $this->approveUser($menu);
            $this->deniedUser($menu);
            $this->removeAuthenticator($menu);
            $this->resendVerificationEmail($menu);
            $this->verifyUserMenu($menu);
        });
    }

    protected function actionVerifyUser(Actions $actions): void
    {
        $actions->add('verifyUser')
            ->apiUrl('admincp/user/verify-user/:id')
            ->asPatch();
    }

    protected function actionApproveUser(Actions $actions): void
    {
        $actions->add('approveUser')
            ->apiUrl('admincp/user/approve/:id')
            ->asPatch();
    }

    protected function actionDeniedUser(Actions $actions): void
    {
        $actions->add('deniedUser')
            ->apiUrl('admincp/core/form/user.deny_user/:id')
            ->asGet();
    }

    protected function actionResendVerificationEmail(Actions $actions): void
    {
        $actions->add('resendVerificationEmail')
            ->apiUrl('admincp/user/resend-verification-email/:id')
            ->asPatch();
    }

    protected function actionRemoveAuthenticator(Actions $actions): void
    {
        $actions->add('removeAuthenticator')
            ->apiUrl('admincp/mfa/authenticator/:id')
            ->asDelete();
    }

    protected function actionFeatureUser(Actions $actions): void
    {
        $actions->add('featureUser')
            ->apiUrl('admincp/user/feature/:id')
            ->apiParams(['feature' => 1])
            ->asPatch();
    }

    protected function actionUnFeatureUser(Actions $actions): void
    {
        $actions->add('unFeatureUser')
            ->apiUrl('admincp/user/feature/:id')
            ->apiParams(['feature' => 0])
            ->asPatch();
    }

    protected function actionBanItem(Actions $actions): void
    {
        $actions->add('banItem')
            ->asGet()
            ->apiUrl('admincp/core/form/user.ban/:id');
    }

    protected function actionUnBanItem(Actions $actions): void
    {
        $actions->add('unBanItem')
            ->apiUrl('admincp/user/ban/:id')
            ->asFormDialog(false)
            ->asDelete();
    }

    protected function actionMoveRole(Actions $actions): void
    {
        $actions->add('batchMoveRole')
            ->asGet()
            ->apiUrl('admincp/core/form/user.batch_move_role?user_ids=[:id]');
    }

    protected function actionBatchApprove(Actions $actions): void
    {
        $actions->add('batchApprove')
            ->asPatch()
            ->asFormDialog(false)
            ->apiUrl('admincp/user/batch-approve?id=[:id]');
    }

    protected function actionBan(Actions $actions): void
    {
        $actions->add('batchBan')
            ->asPost()
            ->asFormDialog(false)
            ->apiParams(['id' => ':id'])
            ->apiUrl('admincp/user/batch-ban')
            ->confirm(['message' => __p('user::phrase.are_you_sure_you_want_to_ban_selected_users')]);
    }

    protected function actionUnBan(Actions $actions): void
    {
        $actions->add('batchUnBan')
            ->asDelete()
            ->asFormDialog(false)
            ->apiUrl('admincp/user/batch-ban?id=[:id]')
            ->confirm(['message' => __p('user::phrase.are_you_sure_you_want_to_un_ban_selected_users')]);
    }

    protected function actionDelete(Actions $actions): void
    {
        $actions->add('batchDelete')
            ->asDelete()
            ->asFormDialog(false)
            ->apiUrl('admincp/user/batch-delete?id=[:id]')
            ->confirm(['message' => __p('user::phrase.are_you_sure_you_want_to_delete_selected_users')]);
    }

    protected function actionBatchVerify(Actions $actions): void
    {
        $actions->add('batchVerify')
            ->asPatch()
            ->asFormDialog(false)
            ->apiUrl('admincp/user/batch-verify?id=[:id]');
    }

    protected function actionBatchResendVerificationEmail(Actions $actions): void
    {
        $actions->add('batchResendVerificationEmail')
            ->asPost()
            ->apiParams(['id' => ':id'])
            ->asFormDialog(false)
            ->apiUrl('admincp/user/batch-resend-verification-email');
    }

    protected function batchMoveRole(BatchActionMenu $menu): void
    {
        $menu->addItem('batchMoveRole')
            ->action('batchMoveRole')
            ->icon('ico-reply-all-alt')
            ->label(__p('user::phrase.move_to_role'))
            ->reload()
            ->asBatchEdit();
    }

    protected function batchApprove(BatchActionMenu $menu): void
    {
        $menu->addItem('batchApprove')
            ->action('batchApprove')
            ->icon('ico-check-circle-o')
            ->label(__p('core::phrase.approve'))
            ->reload()
            ->asBatchEdit();
    }

    protected function batchBan(BatchActionMenu $menu): void
    {
        $menu->addItem('batchBan')
            ->action('batchBan')
            ->icon('ico-lock-o')
            ->label(__p('user::phrase.ban'))
            ->reload()
            ->asBatchEdit();
    }

    protected function batchUnBan(BatchActionMenu $menu): void
    {
        $menu->addItem('batchUnBan')
            ->action('batchUnBan')
            ->icon('ico-unlock-o')
            ->label(__p('user::phrase.unban'))
            ->reload()
            ->asBatchEdit();
    }

    protected function batchDelete(BatchActionMenu $menu): void
    {
        $menu->addItem('batchDelete')
            ->action('batchDelete')
            ->icon('ico-trash')
            ->label(__p('user::phrase.delete'))
            ->reload()
            ->asBatchEdit();
    }

    protected function batchVerify(BatchActionMenu $menu): void
    {
        $menu->addItem('batchVerify')
            ->action('batchVerify')
            ->icon('ico-check-circle-o')
            ->label(__p('user::phrase.verify'))
            ->reload()
            ->asBatchEdit();
    }

    protected function batchResendVerificationEmail(BatchActionMenu $menu): void
    {
        $menu->addItem('batchResendVerificationEmail')
            ->action('batchResendVerificationEmail')
            ->icon('ico-envelope-o')
            ->label(__p('user::phrase.resend_verification_email'))
            ->reload()
            ->asBatchEdit();
    }

    protected function banItem(ItemActionMenu $menu): void
    {
        $menu->addItem('banItem')
            ->action('banItem')
            ->icon('ico-lock-o')
            ->label(__p('user::phrase.ban_user'))
            ->reload()
            ->asEditRow()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_edit'],
                ['falsy', 'item.is_banned'],
            ]);
    }

    protected function unBanItem(ItemActionMenu $menu): void
    {
        $menu->addItem('unBanItem')
            ->action('unBanItem')
            ->icon('ico-lock-o')
            ->label(__p('user::phrase.unban_user'))
            ->reload()
            ->asEditRow()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_edit'],
                ['truthy', 'item.is_banned'],
            ]);
    }

    protected function featureUser(ItemActionMenu $menu): void
    {
        $menu->addItem('featureUser')
            ->action('featureUser')
            ->icon('ico-diamond')
            ->label(__p('user::phrase.feature_user'))
            ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
            ->reload()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_feature'],
                ['falsy', 'item.is_featured'],
            ]);
    }

    protected function unFeatureUser(ItemActionMenu $menu): void
    {
        $menu->addItem('unFeatureUser')
            ->action('unFeatureUser')
            ->icon('ico-diamond')
            ->label(__p('user::phrase.unfeature_user'))
            ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
            ->reload()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_feature'],
                ['truthy', 'item.is_featured'],
            ]);
    }

    protected function approveUser(ItemActionMenu $menu): void
    {
        $menu->addItem('approveUser')
            ->action('approveUser')
            ->icon('ico-lock-o')
            ->label(__p('user::phrase.approve_user'))
            ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
            ->reload()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_edit'],
                ['falsy', 'item.is_approved'],
            ]);
    }

    protected function deniedUser(ItemActionMenu $menu): void
    {
        $menu->addItem('deniedUser')
            ->action('deniedUser')
            ->icon('ico-lock-o')
            ->label(__p('user::phrase.deny_user'))
            ->value(MetaFoxForm::ACTION_ROW_EDIT)
            ->reload()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_edit'],
                ['falsy', 'item.is_approved'],
            ]);
    }

    protected function removeAuthenticator(ItemActionMenu $menu): void
    {
        $menu->addItem('removeAuthenticator')
            ->action('removeAuthenticator')
            ->icon('ico-lock-o')
            ->label(__p('user::phrase.remove_authenticator'))
            ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
            ->reload()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_edit'],
                ['truthy', 'item.is_mfa_enabled'],
            ]);
    }

    protected function resendVerificationEmail(ItemActionMenu $menu): void
    {
        $menu->addItem('resendVerificationEmail')
            ->action('resendVerificationEmail')
            ->icon('ico-envelope-o')
            ->label(__p('user::phrase.resend_verification_email'))
            ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
            ->reload()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_edit'],
                ['falsy', 'item.is_verify_email'],
            ]);
    }

    protected function verifyUserMenu(ItemActionMenu $menu): void
    {
        $menu->addItem('verifyUser')
            ->action('verifyUser')
            ->icon('ico-check-circle-o')
            ->label(__p('user::phrase.verify'))
            ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
            ->reload()
            ->showWhen([
                'and',
                ['truthy', 'item.extra.can_edit'],
                ['falsy', 'item.is_verify_email'],
            ]);
    }
}
