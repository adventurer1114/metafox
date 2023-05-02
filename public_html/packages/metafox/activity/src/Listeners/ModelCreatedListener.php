<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Activity\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Activity\Models\PrivacyMember as ActivityPrivacyMember;
use MetaFox\Activity\Support\Facades\ActivityFeed;
use MetaFox\Activity\Support\Facades\ActivitySubscription;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\IsActivitySubscriptionInterface;
use MetaFox\Platform\Contracts\IsPrivacyMemberInterface;
use MetaFox\User\Models\UserBlocked;

/**
 * Class ModelCreatedListener.
 * @ignore
 */
class ModelCreatedListener
{
    use CheckModeratorSettingTrait;

    /**
     * @param Model $model
     */
    public function handle($model): void
    {
        if ($model instanceof IsActivitySubscriptionInterface) {
            $data = $model->toActivitySubscription();

            if (!empty($data)) {
                ActivitySubscription::addSubscription(...$data);
            }
        }

        ActivityFeed::createFeedFromFeedSource($model);

        // Check if the model is Core Privacy Member, clone to activity privacy member.
        if ($model instanceof IsPrivacyMemberInterface) {
            ActivityPrivacyMember::query()->firstOrCreate([
                'privacy_id' => $model->privacyId(),
                'user_id'    => $model->userId(),
            ]);
        }

        if ($model instanceof UserBlocked) {
            ActivitySubscription::updateSubscription($model->userId(), $model->ownerId(), false);
        }
    }
}
