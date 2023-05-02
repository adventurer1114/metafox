<?php

use Illuminate\Database\Migrations\Migration;
use MetaFox\Chat\Models\Subscription;
use MetaFox\Chat\Repositories\SubscriptionRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;

/*
 * stub: /packages/database/migration.stub
 */

/*
 * @ignore
 * @codeCoverageIgnore
 * @link \$PACKAGE_NAMESPACE$\Models
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $subscriptions = Subscription::query()
            ->where('name', '=', '')
            ->get();
        foreach ($subscriptions as $subscription) {
            $otherSubscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($subscription->room_id, true, $subscription->user_id);
            $user               = UserEntity::getById($otherSubscriptions[0]->user_id)->detail;
            $subscription->update(['name' => $user->full_name]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
    }
};
