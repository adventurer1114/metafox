<?php

namespace MetaFox\QuotaControl\Facades;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Facades\Settings;

class QuotaControl
{
    /**
     * @param User   $user         Context User
     * @param string $entityType   Content Type / Resource Type
     * @param int    $quantityItem Quantity created at a time. Default: 1
     * @param array  $extra        Extra configurations
     *                             [
     *                             'message' => (string) Error message when failed check
     *                             'where' => (array) Supported where param
     *                             ]
     */
    public function checkQuotaControlWhenCreateItem(
        User $user,
        string $entityType,
        int $quantityItem = 1,
        array $extra = [],
    ): void {
        if (!Settings::get('quota.enable', false)) {
            return;
        }

        if ($quantityItem < 1) {
            return;
        }

        $itemQuota = (int) $user->getPermissionValue($entityType . '.quota_control');
        if ($itemQuota <= 0) {
            return;
        }

        $message = Arr::get($extra, 'message') ?? __p('quota::phrase.quota_control_invalid', ['entity_type' => $entityType]);
        $where   = Arr::get($extra, 'where') ?? [];

        $model = Relation::getMorphedModel($entityType);
        if (!$model) {
            return;
        }

        $data = [
            'user_id' => $user->entityId(),
        ];

        // TODO: optimize this
        // use scheduled task to count item statistic
        $totalItem = $model::where(array_merge($where, $data))
            ->count();

        if (null === $totalItem) {
            return;
        }

        if ($totalItem + $quantityItem <= $itemQuota) {
            return;
        }

        $error = json_encode([
            'title'   => __p('quota::phrase.limit_reached'),
            'message' => $message,
        ]);

        abort(403, $error);
    }
}
