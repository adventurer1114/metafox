<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Report\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;
use MetaFox\Report\Models\ReportItem;
use MetaFox\Report\Models\ReportReason;
use MetaFox\Report\Notifications\ProcessedReportItemNotification;
use MetaFox\Report\Policies\Handlers\CanReportItem;
use MetaFox\Report\Policies\Handlers\CanReportToOwner;
use MetaFox\Report\Policies\ReportItemPolicy;
use MetaFox\Report\Policies\ReportReasonPolicy;

class PackageSettingListener extends BasePackageSettingListener
{
    public function getPolicies(): array
    {
        return [
            ReportReason::class => ReportReasonPolicy::class,
            ReportItem::class   => ReportItemPolicy::class,
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            '*' => [
                'report' => UserRole::LEVEL_REGISTERED,
            ],
            ReportReason::ENTITY_TYPE => [
                'view'   => UserRole::LEVEL_REGISTERED,
                'create' => UserRole::LEVEL_STAFF,
                'update' => UserRole::LEVEL_STAFF,
                'delete' => UserRole::LEVEL_STAFF,
            ],
            ReportItem::ENTITY_TYPE => [
                'view'   => UserRole::LEVEL_STAFF,
                'create' => UserRole::LEVEL_REGISTERED,
                'delete' => UserRole::LEVEL_STAFF,
            ],
        ];
    }

    public function getPolicyHandlers(): array
    {
        return [
            'reportItem'    => CanReportItem::class,
            'reportToOwner' => CanReportToOwner::class,
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'processed_report_item',
                'module_id'  => 'report',
                'handler'    => ProcessedReportItemNotification::class,
                'title'      => 'report::phrase.processed_report_item_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail'],
                'ordering'   => 20,
            ],
        ];
    }
}
