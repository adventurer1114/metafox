<?php

namespace MetaFox\Activity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Activity\Models\Post;
use MetaFox\Platform\Support\Factory\HasSetState;

/**
 * Class PostFactory.
 * @ignore
 * @method Post create($attributes = [], ?Model $parent = null)
 */
class PostFactory extends Factory
{
    use HasSetState;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'user_id'            => 1,
            'user_type'          => 'user',
            'owner_id'           => 1,
            'owner_type'         => 'user',
            'privacy'            => 0,
            'content'            => $this->faker->word,
            'location_latitude'  => $this->faker->latitude,
            'location_longitude' => $this->faker->longitude,
            'location_name'      => $this->faker->word,
            'total_like'         => 0,
            'total_comment'      => 0,
            'total_reply'        => 0,
            'total_share'        => 0,
        ];
    }
}
