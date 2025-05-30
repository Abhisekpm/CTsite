<?php

namespace Database\Factories\Backend;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Backend\Testimonial;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backend\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Testimonial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $testimonials = [
            'Amazing cakes! Perfect for our wedding celebration.',
            'The custom chocolate cake was absolutely delicious!',
            'Best bakery in town. Highly recommended!',
            'Beautiful designs and incredible taste.',
            'They made our dream cake come true!',
            'Professional service and outstanding quality.',
            'The cake was the highlight of our party!'
        ];

        return [
            'name' => $this->faker->name(),
            'position' => $this->faker->randomElement([
                'Happy Customer',
                'Wedding Client',
                'Birthday Party Host',
                'Corporate Client',
                'Anniversary Celebrant'
            ]),
            'image' => 'testimonials/' . $this->faker->randomElement([
                'customer1.jpg',
                'customer2.jpg', 
                'customer3.jpg',
                'customer4.jpg'
            ]),
            'quote' => $this->faker->randomElement($testimonials),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the testimonial is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the testimonial is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
} 