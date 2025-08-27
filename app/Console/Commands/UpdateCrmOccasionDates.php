<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrmOccasion;

class UpdateCrmOccasionDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:update-dates {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all CRM occasion dates with new logic that handles recent orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $totalOccasions = CrmOccasion::count();
        
        if (!$this->option('force')) {
            if (!$this->confirm("This will recalculate dates for {$totalOccasions} occasions. Continue?")) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info("Updating dates for {$totalOccasions} occasions...");
        $progressBar = $this->output->createProgressBar($totalOccasions);
        $progressBar->start();

        $updatedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        CrmOccasion::chunk(100, function ($occasions) use (&$updatedCount, &$skippedCount, &$errorCount, $progressBar) {
            foreach ($occasions as $occasion) {
                try {
                    // Store old values for comparison
                    $oldNextDate = $occasion->next_anticipated_order_date;
                    $oldAnchorWeek = $occasion->next_anchor_week_start;
                    
                    // Recalculate dates
                    $occasion->next_anticipated_order_date = $occasion->calculateNextAnticipatedOrderDate();
                    $occasion->updateCalculatedDates();
                    $occasion->save();
                    
                    // Check if anything actually changed
                    if ($oldNextDate != $occasion->next_anticipated_order_date || 
                        $oldAnchorWeek != $occasion->next_anchor_week_start) {
                        $updatedCount++;
                    } else {
                        $skippedCount++;
                    }
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("Error updating occasion ID {$occasion->id}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Update complete!");
        $this->table(['Status', 'Count'], [
            ['Updated (changed)', $updatedCount],
            ['Skipped (no change)', $skippedCount],
            ['Errors', $errorCount],
            ['Total processed', $totalOccasions]
        ]);

        if ($errorCount > 0) {
            $this->warn("There were {$errorCount} errors during processing. Check the output above for details.");
        }

        return Command::SUCCESS;
    }
}
