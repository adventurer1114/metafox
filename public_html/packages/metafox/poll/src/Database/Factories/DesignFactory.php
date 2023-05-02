<?php

namespace MetaFox\Poll\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MetaFox\Poll\Models\Design;
use MetaFox\Poll\Models\Poll;

class DesignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Design::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'border'     => $this->faker->numberBetween(1, 4),
            'percentage' => $this->faker->numberBetween(0, 100),
            'background' => $this->faker->hexColor,
        ];
    }

    /**
     * @param  Poll          $poll
     * @return DesignFactory
     */
    public function setPoll(Poll $poll): self
    {
        return $this->state(function () use ($poll) {
            return [
                'id' => $poll->entityId(),
            ];
        });
    }
}
