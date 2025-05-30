<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CustomOrderImage;
use App\Models\CustomOrder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomOrderImage>
 */
class CustomOrderImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomOrderImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageNames = [
            'custom_orders/cake_design_1.jpg',
            'custom_orders/cake_design_2.png',
            'custom_orders/decoration_idea_1.jpg',
            'custom_orders/decoration_idea_2.png',
            'custom_orders/theme_reference_1.jpg',
            'custom_orders/theme_reference_2.png',
            'custom_orders/color_scheme_1.jpg',
            'custom_orders/inspiration_1.png',
            'custom_orders/sample_design_1.jpg',
            'custom_orders/sample_design_2.png',
        ];

        return [
            'custom_order_id' => CustomOrder::factory(),
            'path' => $this->faker->randomElement($imageNames),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the image belongs to a specific custom order.
     */
    public function forOrder(CustomOrder $order): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_order_id' => $order->id,
        ]);
    }

    /**
     * Create an image with a specific file path.
     */
    public function withPath(string $path): static
    {
        return $this->state(fn (array $attributes) => [
            'path' => $path,
        ]);
    }

    /**
     * Create a JPEG image.
     */
    public function jpeg(): static
    {
        return $this->state(fn (array $attributes) => [
            'path' => 'custom_orders/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a PNG image.
     */
    public function png(): static
    {
        return $this->state(fn (array $attributes) => [
            'path' => 'custom_orders/' . $this->faker->uuid() . '.png',
        ]);
    }
} 