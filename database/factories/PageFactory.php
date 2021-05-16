<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'                => $this->faker->sentence(2),
            'slug'                 => $this->faker->slug(),
            'body'                 => $this->faker->paragraph(100),
            'is_default_home'      => false,
            'is_default_not_found' => false,
        ];
    }
}
