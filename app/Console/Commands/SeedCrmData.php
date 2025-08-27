<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CrmCustomersSeeder;
use Database\Seeders\CrmOccasionsSeeder;

class SeedCrmData extends Command
{
    protected $signature = 'crm:seed {--customers : Seed only customers} {--occasions : Seed only occasions} {--fresh : Clear existing data first}';
    protected $description = 'Seed CRM customers and occasions data from CSV files';

    public function handle()
    {
        $seedCustomers = $this->option('customers') || (!$this->option('customers') && !$this->option('occasions'));
        $seedOccasions = $this->option('occasions') || (!$this->option('customers') && !$this->option('occasions'));
        $fresh = $this->option('fresh');

        if ($fresh) {
            if ($this->confirm('This will delete all existing CRM data. Continue?')) {
                $this->info('Clearing existing CRM data...');
                \App\Models\CrmOccasion::truncate();
                \App\Models\CrmCustomer::truncate();
            } else {
                $this->info('Cancelled.');
                return Command::SUCCESS;
            }
        }

        if ($seedCustomers) {
            $this->info('Seeding CRM customers...');
            $this->call('db:seed', ['--class' => CrmCustomersSeeder::class]);
        }

        if ($seedOccasions) {
            $this->info('Seeding CRM occasions...');
            $this->call('db:seed', ['--class' => CrmOccasionsSeeder::class]);
        }

        $this->info('CRM seeding completed successfully!');
        
        // Show summary
        $customerCount = \App\Models\CrmCustomer::count();
        $occasionCount = \App\Models\CrmOccasion::count();
        $weeklyGroups = \App\Models\CrmOccasion::distinct('anchor_week_start_date')->count();
        
        $this->table(['Metric', 'Count'], [
            ['Total Customers', $customerCount],
            ['Total Occasions', $occasionCount],
            ['Weekly Groups', $weeklyGroups],
        ]);

        return Command::SUCCESS;
    }
}
