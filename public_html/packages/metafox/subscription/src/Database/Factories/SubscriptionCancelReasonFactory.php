<?php

namespace MetaFox\Subscription\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Support\Factory\HasSetState;
use MetaFox\Subscription\Models\SubscriptionCancelReason;

/**
 * stub: /packages/database/factory.stub.
 */

/**
 * Class SubscriptionCancelReasonFactory.
 * @method SubscriptionCancelReason create($attributes = [], ?Model $parent = null)
 * @ignore
 */
class SubscriptionCancelReasonFactory extends Factory
{
    use HasSetState;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscriptionCancelReason::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}

// end
