<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrmOccasion;
use Carbon\Carbon;

class ImportExtractedOccasions extends Command
{
    protected $signature = 'crm:import-extracted-occasions';
    protected $description = 'Import extracted occasions from CSV file';

    public function handle()
    {
        $this->info('=== OCCASIONS DATABASE IMPORT ===');
        
        // Step 1: Check current data
        $currentCount = CrmOccasion::count();
        $this->info("Current occasions in database: {$currentCount}");
        
        // Step 2: Clear existing occasions
        $this->info('Clearing existing occasions...');
        CrmOccasion::truncate();
        $this->info('✅ Occasions table cleared!');
        
        // Step 3: Load CSV file
        $filePath = base_path('CRM/extracted_occasions.csv');
        $this->info("Loading CSV file: {$filePath}");
        
        if (!file_exists($filePath)) {
            $this->error("CSV file not found: {$filePath}");
            return 1;
        }
        
        // Step 4: Import data
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle); // Skip header
        
        $imported = 0;
        $errors = 0;
        $progressBar = $this->output->createProgressBar();
        
        $this->info('Starting import...');
        
        while (($row = fgetcsv($handle)) !== false) {
            try {
                // Skip rows without essential data
                if (empty($row[0]) || empty($row[3])) {
                    continue;
                }
                
                CrmOccasion::create([
                    'customer_id' => $row[0],
                    'occasion_type' => $row[3],
                    'honoree_name' => !empty($row[4]) ? $row[4] : null,
                    'anchor_confidence' => !empty($row[5]) ? $row[5] : 'medium',
                    'last_order_date_latest' => !empty($row[6]) ? Carbon::parse($row[6]) : null,
                    'history_count' => (int)(!empty($row[7]) ? $row[7] : 1),
                    'history_years' => !empty($row[8]) ? $row[8] : null,
                    'anchor_week_start_date' => !empty($row[9]) ? Carbon::parse($row[9]) : null,
                    'next_anticipated_order_date' => !empty($row[10]) ? Carbon::parse($row[10]) : null,
                    'reminder_date' => !empty($row[11]) ? Carbon::parse($row[11]) : null,
                    'reminder_sent' => false,
                    'notes' => !empty($row[13]) ? $row[13] : null,
                ]);
                
                $imported++;
                $progressBar->advance();
                
            } catch (\Exception $e) {
                $errors++;
                if ($errors <= 3) {
                    $this->warn("Error importing row: " . $e->getMessage());
                }
            }
        }
        
        fclose($handle);
        $progressBar->finish();
        
        $this->newLine();
        $this->info('🎉 Import completed!');
        $this->info("📈 Imported: {$imported} occasions");
        $this->info("❌ Errors: {$errors}");
        
        // Verification
        $this->info('🔍 Verifying data...');
        $totalCount = CrmOccasion::count();
        $birthdayCount = CrmOccasion::where('occasion_type', 'birthday')->count();
        $anniversaryCount = CrmOccasion::where('occasion_type', 'anniversary')->count();
        $futureDates = CrmOccasion::where('next_anticipated_order_date', '>', now())->count();
        $withHonorees = CrmOccasion::whereNotNull('honoree_name')->where('honoree_name', '!=', '')->count();
        
        $this->info("✅ Total occasions: {$totalCount}");
        $this->info("🎂 Birthday occasions: {$birthdayCount}");
        $this->info("💍 Anniversary occasions: {$anniversaryCount}");
        $this->info("📅 Future dates: {$futureDates}");
        $this->info("👤 With honoree names: {$withHonorees}");
        
        $this->info('🌟 Database import completed successfully!');
        
        return Command::SUCCESS;
    }
}
