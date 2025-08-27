<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrmOccasion;
use Carbon\Carbon;

class RecalculateOccasionDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:recalculate-occasions {--force : Force recalculation even if next_anticipated_order_date exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate next_anticipated_order_date and related dates for all CRM occasions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting occasion date recalculation...');

        $force = $this->option('force');
        
        // Get occasions that need recalculation
        $query = CrmOccasion::whereNotNull('last_order_date_latest');
        
        if (!$force) {
            $query->whereNull('next_anticipated_order_date');
        }
        
        $occasions = $query->get();
        
        $this->info("Found {$occasions->count()} occasions to process");
        
        if ($occasions->isEmpty()) {
            $this->warn('No occasions found that need recalculation.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($occasions->count());
        $progressBar->start();

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($occasions as $occasion) {
            try {
                $originalNextDate = $occasion->next_anticipated_order_date;
                
                // Calculate and update all dates
                $occasion->updateCalculatedDates();
                $occasion->save();

                if ($originalNextDate != $occasion->next_anticipated_order_date) {
                    $updated++;
                } else {
                    $skipped++;
                }

            } catch (\Exception $e) {
                $this->error("\nError processing occasion ID {$occasion->id}: " . $e->getMessage());
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Show summary
        $this->info("Recalculation completed!");
        $this->table(['Metric', 'Count'], [
            ['Updated', $updated],
            ['Skipped (no change)', $skipped],
            ['Errors', $errors],
            ['Total Processed', $occasions->count()]
        ]);

        // Show some example results
        $this->info("\nSample results:");
        $sampleOccasions = CrmOccasion::whereNotNull('next_anticipated_order_date')
            ->with('customer')
            ->limit(5)
            ->get();

        foreach ($sampleOccasions as $occasion) {
            $this->line("Customer: {$occasion->customer->buyer_name} | " .
                      "Type: {$occasion->occasion_type} | " .
                      "Next Date: {$occasion->next_anticipated_order_date->format('M d, Y')} | " .
                      "Anchor Week: {$occasion->anchor_week_start_date->format('M d, Y')}");
        }

        return Command::SUCCESS;
    }
}
