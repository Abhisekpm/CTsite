<?php

namespace Database\Factories\Backend;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Backend\Menu;
use App\Models\Backend\MenuCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backend\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cakeFlavors = [
            'Chocolate Fudge',
            'Vanilla Bean',
            'Red Velvet',
            'Carrot Spice',
            'Lemon Zest',
            'Strawberry',
            'Funfetti',
            'Black Forest'
        ];

        return [
            'name' => $this->faker->randomElement($cakeFlavors),
            'menu_category_id' => MenuCategory::factory(),
            'description' => $this->faker->sentence(8),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 