<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Backend\Settings;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if settings already exist
        if (Settings::count() == 0) {
            Settings::create([
                'website_name' => 'Chocolate Therapy',
                'logo' => 'logo-black.png',
                'favicon' => 'favicon.png',
                'meta_title' => 'Chocolate Therapy - Custom Cakes & Desserts',
                'meta_keywords' => 'chocolate, cake, custom cakes, desserts, bakery',
                'meta_description' => 'Premium custom cakes and chocolate desserts made with love for your special occasions.',
            ]);
        }
    }
} 