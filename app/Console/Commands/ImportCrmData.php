<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrmCustomer;
use App\Models\CrmOccasion;
use Carbon\Carbon;
use League\Csv\Reader;

class ImportCrmData extends Command
{
    protected $signature = 'crm:import {type} {file} {--convert-weekly : Convert daily occasions to weekly groups}';
    protected $description = 'Import CRM data from CSV files with weekly grouping support';

    public function handle()
    {
        $type = $this->argument('type');
        $file = $this->argument('file');
        $convertWeekly = $this->option('convert-weekly');
        
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return Command::FAILURE;
        }
        
        if ($type === 'customers') {
            return $this->importCustomers($file);
        } elseif ($type === 'occasions') {
            return $this->importOccasions($file, $convertWeekly);
        } else {
            $this->error('Invalid type. Use "customers" or "occasions"');
            return Command::FAILURE;
        }
    }
    
    private function importCustomers($file)
    {
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);
        
        $this->info('Importing customers...');
        $count = 0;
        
        foreach ($csv as $record) {
            try {
                CrmCustomer::updateOrCreate(
                    ['customer_id' => $record['customer_id']],
                    [
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
                    ]
                );
                $count++;
                
                if ($count % 100 == 0) {
                    $this->info("Processed {$count} customers...");
                }
            } catch (\Exception $e) {
                $this->warn("Error importing customer {$record['customer_id']}: " . $e->getMessage());
            }
        }
        
        $this->info("Successfully imported {$count} customers.");
        return Command::SUCCESS;
    }
    
    private function importOccasions($file, $convertWeekly = true)
    {
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);
        
        $this->info('Importing occasions with weekly grouping...');
        $count = 0;
        $weeklyGroups = [];
        
        foreach ($csv as $record) {
            try {
                // Calculate next_anticipated_order_date first
                $lastOrderDate = !empty($record['last_order_date_latest']) ? 
                    Carbon::parse($record['last_order_date_latest']) : null;
                
                $nextAnticipatedOrderDate = null;
                if ($lastOrderDate) {
                    $nextAnticipatedOrderDate = CrmOccasion::calculateNextAnticipatedOrderDateStatic($lastOrderDate);
                }
                
                if ($convertWeekly && isset($record['anchor_month']) && isset($record['anchor_day'])) {
                    // Convert anchor_month/anchor_day to weekly grouping
                    $anchorWeekStart = $this->calculateAnchorWeekStart(
                        (int)$record['anchor_month'], 
                        (int)$record['anchor_day']
                    );
                    $reminderDate = $this->calculateReminderDate($anchorWeekStart);
                    $nextAnchorWeekStart = $anchorWeekStart->copy()->addYear();
                } elseif ($nextAnticipatedOrderDate) {
                    // Use the next_anticipated_order_date to calculate anchor week
                    $anchorWeekStart = $nextAnticipatedOrderDate->copy()->startOfWeek(Carbon::MONDAY);
                    $reminderDate = $anchorWeekStart->copy()->subDays(8);
                    $nextAnchorWeekStart = $anchorWeekStart;
                } else {
                    // Use existing data if already in weekly format
                    $anchorWeekStart = Carbon::parse($record['anchor_week_start_date']);
                    $reminderDate = Carbon::parse($record['reminder_date']);
                    $nextAnchorWeekStart = Carbon::parse($record['next_anchor_week_start']);
                }
                
                CrmOccasion::updateOrCreate(
                    [
                        'customer_id' => $record['customer_id'],
                        'occasion_type' => $record['occasion_type'],
                        'anchor_week_start_date' => $anchorWeekStart->toDateString(),
                    ],
                    [
                        'honoree_name' => $record['honoree_name'] ?? null,
                        'anchor_window_days' => (int)($record['anchor_window_days'] ?? 7),
                        'anchor_confidence' => $record['anchor_confidence'] ?? 'high',
                        'last_order_date_latest' => $lastOrderDate,
                        'next_anticipated_order_date' => $nextAnticipatedOrderDate,
                        'history_count' => (int)($record['history_count'] ?? 1),
                        'history_years' => $record['history_years'] ?? null,
                        'source_occasion_ids' => $record['source_occasion_ids'] ?? null,
                        'next_anchor_week_start' => $nextAnchorWeekStart,
                        'reminder_date' => $reminderDate,
                        'reminder_sent' => false,
                    ]
                );
                $count++;
                
                // Track weekly groupings for summary
                $weekKey = $anchorWeekStart->toDateString();
                $weeklyGroups[$weekKey] = ($weeklyGroups[$weekKey] ?? 0) + 1;
                
                if ($count % 100 == 0) {
                    $this->info("Processed {$count} occasions...");
                }
            } catch (\Exception $e) {
                $this->warn("Error importing occasion for {$record['customer_id']}: " . $e->getMessage());
            }
        }
        
        $this->info("Successfully imported {$count} occasions into " . count($weeklyGroups) . " weekly groups.");
        
        // Show top 10 weekly groups
        $topWeeks = collect($weeklyGroups)->sortByDesc(function($count) {
            return $count;
        })->take(10);
        
        $this->table(['Week Start (Monday)', 'Occasions Count'], 
            $topWeeks->map(fn($count, $week) => [$week, $count])->toArray());
        
        return Command::SUCCESS;
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
