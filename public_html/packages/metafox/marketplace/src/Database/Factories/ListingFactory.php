<?php

namespace MetaFox\Marketplace\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Platform\Support\Factory\HasSetState;

/**
 * Class ListingFactory.
 * @method Listing create($attributes = [], ?Model $parent = null)
 * @ignore
 * @codeCoverageIgnore
 */
class ListingFactory extends Factory
{
    use HasSetState;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Listing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $text = $this->faker->text;

        return [
            'is_approved'         => 1,
            'privacy'             => random_privacy(),
            'user_id'             => 1,
            'user_type'           => 'user',
            'owner_id'            => 1,
            'owner_type'          => 'user',
            'is_featured'         => 0,
            'is_sponsor'          => 0,
            'sponsor_in_feed'     => 0,
            'allow_payment'       => $this->faker->numberBetween(0, 1),
            'allow_point_payment' => $this->faker->numberBetween(0, 1),
            'is_sold'             => 0,
            'is_notified'         => $this->faker->numberBetween(0, 1),
            'price'               => '{}',
            'title'               => $this->faker->sentence,
            'short_description'   => $this->faker->realTextBetween(10, 100),
            'location_latitude'   => $this->faker->numberBetween(0, 100),
            'location_longitude'  => $this->faker->numberBetween(0, 5000),
            'location_name'       => $this->faker->address,
            'country_iso'         => $this->faker->countryCode,
            'text'                => $text,
            'total_like'          => 0,
            'total_share'         => 0,
            'total_comment'       => 0,
            'total_attachment'    => 0,
            'total_view'          => 0,
        ];
    }
}
