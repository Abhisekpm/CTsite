<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CrmCustomer;
use Carbon\Carbon;
use League\Csv\Reader;

class CrmCustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding CRM Customers...');
        
        // Path to CSV file
        $csvPath = database_path('seeders/data/customers_master.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            $this->command->info("Please copy CRM/customers_master.csv to database/seeders/data/customers_master.csv");
            return;
        }
        
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        
        $count = 0;
        $batch = [];
        
        foreach ($csv as $record) {
            try {
                $batch[] = [
                    'customer_id' => $record['customer_id'],
                    'buyer_name' => $record['buyer_name'] ?? '',
                    'primary_phone' => $record['primary_phone'] ?? null,
                    'primary_email' => $record['primary_email'] ?? $record['customer_id'],
                    'first_order' => !empty($record['first_order']) ? Carbon::parse($record['first_order']) : null,
                    'last_order' => !empty($record['last_order']) ? Carbon::parse($record['last_order']) : null,
                    'orders_count' => (int)($record['orders_count'] ?? 0),
                    'fav_flavors' => $record['fav_flavors'] ?? null,
                    'eggs_ok' => $record['eggs_ok'] ?? '',
                    'allergens' => $record['allergens'] ?? null,
                    'marketing_opt_in' => !empty($record['marketing_opt_in']),
                    'channel_preference' => $record['channel_preference'] ?? null,
                    'notes' => $record['notes'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $count++;
                
                // Insert in batches of 100 for performance
                if (count($batch) >= 100) {
                    CrmCustomer::upsert($batch, ['customer_id'], [
                        'buyer_name', 'primary_phone', 'primary_email', 'first_order', 
                        'last_order', 'orders_count', 'fav_flavors', 'eggs_ok', 
                        'allergens', 'marketing_opt_in', 'channel_preference', 'notes'
                    ]);
                    $batch = [];
                    $this->command->info("Processed {$count} customers...");
                }
            } catch (\Exception $e) {
                $this->command->warn("Error processing customer {$record['customer_id']}: " . $e->getMessage());
            }
        }
        
        // Insert remaining records
        if (count($batch) > 0) {
            CrmCustomer::upsert($batch, ['customer_id'], [
                'buyer_name', 'primary_phone', 'primary_email', 'first_order', 
                'last_order', 'orders_count', 'fav_flavors', 'eggs_ok', 
                'allergens', 'marketing_opt_in', 'channel_preference', 'notes'
            ]);
        }
        
        $this->command->info("Successfully seeded {$count} CRM customers.");
    }
}
