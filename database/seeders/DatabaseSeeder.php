<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Create a default user
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            // Password will typically be 'password' if using standard factory
        ]);

        // CRM Data Seeders
        $this->call([
            CrmCustomersSeeder::class,
            CrmOccasionsSeeder::class,
        ]);
    }
}
