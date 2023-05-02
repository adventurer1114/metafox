<?php

namespace MetaFox\Marketplace\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MetaFox\Marketplace\Models\Image;

/**
 * Class ImageFactory.
 * @ignore
 * @codeCoverageIgnore
 */
class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'image_path'=> '/path/to/image.jpg',
            'server_id' => 'public',
            'ordering'  => 0,
        ];
    }
}
