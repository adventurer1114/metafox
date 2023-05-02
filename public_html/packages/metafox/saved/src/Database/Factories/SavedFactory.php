<?php

namespace MetaFox\Saved\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Factory\HasSetState;
use MetaFox\Saved\Models\Saved;

/**
 * Class SavedFactory.
 * @method Saved create($attributes = [], ?Model $parent = null)
 * @method self  setItem(Content $item)
 * @ignore
 * @codeCoverageIgnore
 */
class SavedFactory extends Factory
{
    use HasSetState;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Saved::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_opened'  => 0,
            'savedLists' => [],
        ];
    }

    public function setOwner(User $user)
    {
        return $this;
    }
}
