<?php

namespace Database\Factories;

use App\Models\NavMenu;
use Illuminate\Database\Eloquent\Factories\Factory;

class NavMenuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NavMenu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'label'    => $this->faker->sentence(1),
            'sequence' => $this->faker->unique()->numberBetween(1, 10),
            'type'     => $this->faker->randomElement(['side', 'top']),
            'slug'     => $this->faker->slug(),
        ];
    }
}
