<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CrmOccasion;
use Carbon\Carbon;
use League\Csv\Reader;

class CrmOccasionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding CRM Occasions with weekly grouping...');
        
        // Path to CSV file
        $csvPath = database_path('seeders/data/occasions_master.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            $this->command->info("Please copy CRM/occasions_master.csv to database/seeders/data/occasions_master.csv");
            return;
        }
        
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        
        $count = 0;
        $batch = [];
        $weeklyGroups = [];
        
        foreach ($csv as $record) {
            try {
                // Convert anchor_month/anchor_day to weekly grouping
                if (isset($record['anchor_month']) && isset($record['anchor_day'])) {
                    $anchorWeekStart = $this->calculateAnchorWeekStart(
                        (int)$record['anchor_month'], 
                        (int)$record['anchor_day']
                    );
                    $reminderDate = $this->calculateReminderDate($anchorWeekStart);
                    $nextAnchorWeekStart = $anchorWeekStart->copy()->addYear();
                } else {
                    // Use existing weekly data if available
                    $anchorWeekStart = Carbon::parse($record['anchor_week_start_date']);
                    $reminderDate = Carbon::parse($record['reminder_date']);
                    $nextAnchorWeekStart = Carbon::parse($record['next_anchor_week_start']);
                }
                
                $batch[] = [
                    'customer_id' => $record['customer_id'],
                    'occasion_type' => $record['occasion_type'],
                    'honoree_name' => $record['honoree_name'] ?? null,
                    'anchor_week_start_date' => $anchorWeekStart->toDateString(),
                    'anchor_window_days' => (int)($record['anchor_window_days'] ?? 7),
                    'anchor_confidence' => $record['anchor_confidence'] ?? 'high',
                    'last_order_date_latest' => !empty($record['last_order_date_latest']) ? 
                        Carbon::parse($record['last_order_date_latest']) : null,
                    'history_count' => (int)($record['history_count'] ?? 1),
                    'history_years' => $record['history_years'] ?? null,
                    'source_occasion_ids' => $record['source_occasion_ids'] ?? null,
                    'next_anchor_week_start' => $nextAnchorWeekStart,
                    'reminder_date' => $reminderDate,
                    'reminder_sent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $count++;
                
                // Track weekly groupings for summary
                $weekKey = $anchorWeekStart->toDateString();
                $weeklyGroups[$weekKey] = ($weeklyGroups[$weekKey] ?? 0) + 1;
                
                // Insert in batches of 100 for performance
                if (count($batch) >= 100) {
                    CrmOccasion::upsert($batch, 
                        ['customer_id', 'occasion_type', 'anchor_week_start_date'], 
                        [
                            'honoree_name', 'anchor_window_days', 'anchor_confidence',
                            'last_order_date_latest', 'history_count', 'history_years',
                            'source_occasion_ids', 'next_anchor_week_start', 'reminder_date'
                        ]
                    );
                    $batch = [];
                    $this->command->info("Processed {$count} occasions...");
                }
            } catch (\Exception $e) {
                $this->command->warn("Error processing occasion for {$record['customer_id']}: " . $e->getMessage());
            }
        }
        
        // Insert remaining records
        if (count($batch) > 0) {
            CrmOccasion::upsert($batch, 
                ['customer_id', 'occasion_type', 'anchor_week_start_date'], 
                [
                    'honoree_name', 'anchor_window_days', 'anchor_confidence',
                    'last_order_date_latest', 'history_count', 'history_years',
                    'source_occasion_ids', 'next_anchor_week_start', 'reminder_date'
                ]
            );
        }
        
        $this->command->info("Successfully seeded {$count} occasions into " . count($weeklyGroups) . " weekly groups.");
        
        // Show summary of weekly groups
        $topWeeks = collect($weeklyGroups)->sortByDesc(function($count) {
            return $count;
        })->take(5);
        
        $this->command->table(['Week Start (Monday)', 'Occasions Count'], 
            $topWeeks->map(fn($count, $week) => [$week, $count])->toArray());
    }
    
    private function calculateAnchorWeekStart($month, $day, $year = null)
    {
        $year = $year ?? now()->year;
        $anchorDate = Carbon::createFromDate($year, $month, $day);
        return $anchorDate->startOfWeek(Carbon::MONDAY);
    }
    
    private function calculateReminderDate($anchorWeekStart)
    {
        return $anchorWeekStart->copy()->subDays(8); // Sunday 8 days before
    }
}
