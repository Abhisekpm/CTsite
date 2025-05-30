<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CustomOrder;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomOrder>
 */
class CustomOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cakeSizes = ['6 inch', '8 inch', '10 inch', '12 inch', 'Half Sheet', 'Full Sheet', 'Cupcakes (12)', 'Cupcakes (24)'];
        $cakeFlavors = ['Vanilla', 'Chocolate', 'Red Velvet', 'Carrot', 'Lemon', 'Strawberry', 'Funfetti', 'Chocolate Chip'];
        $cakeSponges = ['Eggless', 'Regular', 'Whole Wheat', 'Gluten Free'];
        $statuses = ['pending', 'priced', 'confirmed', 'cancelled'];
        $eggsOptions = ['Yes', 'No'];

        return [
            'customer_name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->e164PhoneNumber(), // International format
            'pickup_date' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'pickup_time' => $this->faker->time('H:i'),
            'cake_size' => $this->faker->randomElement($cakeSizes),
            'cake_flavor' => $this->faker->randomElement($cakeFlavors),
            'cake_sponge' => $this->faker->randomElement($cakeSponges),
            'eggs_ok' => $this->faker->randomElement($eggsOptions),
            'message_on_cake' => $this->faker->optional(0.7)->text(100), // 70% chance of having a message
            'custom_decoration' => $this->faker->optional(0.8)->paragraph(), // 80% chance of custom decoration
            'allergies' => $this->faker->optional(0.3)->sentence(), // 30% chance of allergies
            'status' => $this->faker->randomElement($statuses),
            'price' => $this->faker->optional(0.6)->numberBetween(2500, 15000), // Price in cents, 60% chance
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'price' => null,
        ]);
    }

    /**
     * Indicate that the order is priced.
     */
    public function priced(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'priced',
            'price' => $this->faker->numberBetween(2500, 15000),
        ]);
    }

    /**
     * Indicate that the order is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'price' => $this->faker->numberBetween(2500, 15000),
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the order is for today's pickup.
     */
    public function todayPickup(): static
    {
        return $this->state(fn (array $attributes) => [
            'pickup_date' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the order is for future pickup.
     */
    public function futurePickup(): static
    {
        return $this->state(fn (array $attributes) => [
            'pickup_date' => $this->faker->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the order is for past pickup (for testing past orders).
     */
    public function pastPickup(): static
    {
        return $this->state(fn (array $attributes) => [
            'pickup_date' => $this->faker->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the order has allergies.
     */
    public function withAllergies(): static
    {
        return $this->state(fn (array $attributes) => [
            'allergies' => $this->faker->randomElement([
                'Nuts allergy',
                'Dairy free required',
                'Gluten intolerance',
                'Egg allergy',
                'No artificial colors',
                'Lactose intolerant',
                'Vegan preferred'
            ]),
        ]);
    }

    /**
     * Indicate that the order has custom decoration.
     */
    public function withCustomDecoration(): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_decoration' => $this->faker->randomElement([
                'Pink and gold theme with roses',
                'Princess castle design',
                'Sports theme with football',
                'Unicorn with rainbow colors',
                'Simple elegant white and silver',
                'Baby shower with blue elephants',
                'Birthday with number candles'
            ]),
        ]);
    }

    /**
     * Indicate that the order has a message on cake.
     */
    public function withMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'message_on_cake' => $this->faker->randomElement([
                'Happy Birthday!',
                'Congratulations!',
                'Happy Anniversary',
                'Welcome Baby',
                'Get Well Soon',
                'Thank You',
                'Happy Retirement'
            ]),
        ]);
    }
} 