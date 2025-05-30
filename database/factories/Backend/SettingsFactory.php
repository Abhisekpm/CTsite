<?php

namespace Database\Factories\Backend;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Backend\Settings;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Backend\Settings>
 */
class SettingsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Settings::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'website_name' => 'Chocolate Therapy',
            'logo' => 'logo-black.png',
            'favicon' => 'favicon.png',
            'meta_title' => 'Chocolate Therapy - Custom Cakes & Desserts',
            'meta_keywords' => 'chocolate, cake, custom cakes, desserts, bakery',
            'meta_description' => 'Premium custom cakes and chocolate desserts made with love for your special occasions.',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 