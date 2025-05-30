<?php

namespace Database\Factories\Backend;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Backend\MenuCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backend\MenuCategory>
 */
class MenuCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MenuCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Cakes',
                'Cupcakes', 
                'Cookies',
                'Pastries',
                'Desserts',
                'Chocolates'
            ]),
            'slug' => function (array $attributes) {
                return strtolower(str_replace(' ', '-', $attributes['name']));
            },
            'order' => $this->faker->numberBetween(1, 10),
            'image' => 'menu/' . $this->faker->randomElement([
                'category1.jpg',
                'category2.jpg',
                'category3.jpg'
            ]),
            'description' => $this->faker->sentence(10),
            'meta_title' => function (array $attributes) {
                return $attributes['name'] . ' - Chocolate Therapy';
            },
            'meta_keywords' => function (array $attributes) {
                return strtolower($attributes['name']) . ', bakery, chocolate therapy';
            },
            'meta_description' => function (array $attributes) {
                return 'Delicious ' . strtolower($attributes['name']) . ' made fresh daily at Chocolate Therapy.';
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create a cakes category.
     */
    public function cakes(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cakes',
            'slug' => 'cakes',
        ]);
    }
} 