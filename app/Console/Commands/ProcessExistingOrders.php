<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomOrder;
use App\Services\CrmOrderIntegrationService;

class ProcessExistingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:process-existing-orders {--dry-run : Show what orders would be processed without actually processing them} {--status=confirmed : Order status to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process existing confirmed orders through CRM integration (for deployment)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $status = $this->option('status');
        $isDryRun = $this->option('dry-run');

        $this->info("Processing existing orders with status: {$status}");
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No actual processing will occur');
        }

        // Get all confirmed orders
        $orders = CustomOrder::where('status', $status)
            ->whereNotNull('email')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            $this->warn("No orders found with status '{$status}'");
            return Command::SUCCESS;
        }

        $this->info("Found {$orders->count()} orders to process:");
        $this->newLine();

        // Show orders in a table
        $this->table(
            ['ID', 'Customer', 'Email', 'Pickup Date', 'Created', 'Message'],
            $orders->map(function ($order) {
                return [
                    $order->id,
                    $order->customer_name,
                    $order->email,
                    $order->pickup_date,
                    $order->created_at->format('M d, Y'),
                    \Str::limit($order->message_on_cake ?? '', 30)
                ];
            })->toArray()
        );

        if ($isDryRun) {
            $this->info('DRY RUN: These orders would be processed through CRM integration.');
            return Command::SUCCESS;
        }

        if (!$this->confirm('Process these orders through CRM integration?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $processedCount = 0;
        $errorCount = 0;
        $service = app(CrmOrderIntegrationService::class);

        $progressBar = $this->output->createProgressBar($orders->count());
        $progressBar->start();

        foreach ($orders as $order) {
            try {
                $service->processConfirmedOrder($order);
                $processedCount++;
                $this->line("\n✅ Processed order #{$order->id} - {$order->customer_name}");
            } catch (\Exception $e) {
                $errorCount++;
                $this->line("\n❌ Failed to process order #{$order->id}: " . $e->getMessage());
                \Log::error("Batch CRM Processing: Failed to process order #{$order->id}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Processing completed:");
        $this->line("✅ Successfully processed: {$processedCount} orders");
        
        if ($errorCount > 0) {
            $this->error("❌ Failed to process: {$errorCount} orders");
        }

        $this->newLine();
        $this->info('You can now verify the results in the CRM Dashboard.');

        return Command::SUCCESS;
    }
}