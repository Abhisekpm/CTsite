<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomOrder;
use App\Models\CrmCustomer;
use App\Models\CrmOccasion;
use App\Services\CrmOrderIntegrationService;

class TestCrmIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:test-integration {order_id? : The order ID to test with} {--create-sample : Create a sample order for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test CRM integration with order confirmation process';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('create-sample')) {
            return $this->createSampleOrder();
        }

        $orderId = $this->argument('order_id');
        
        if (!$orderId) {
            // Show available confirmed orders
            $this->showAvailableOrders();
            $orderId = $this->ask('Enter the order ID you want to test with');
        }

        $order = CustomOrder::find($orderId);
        
        if (!$order) {
            $this->error("Order with ID {$orderId} not found.");
            return Command::FAILURE;
        }

        $this->info("Testing CRM integration for Order #{$order->id}");
        $this->line("Customer: {$order->customer_name} ({$order->email})");
        $this->line("Pickup Date: {$order->pickup_date}");
        $this->line("Message: {$order->message_on_cake}");
        $this->line("Decoration: {$order->custom_decoration}");
        $this->newLine();

        // Show current CRM state
        $this->showCurrentCrmState($order);

        if ($this->confirm('Proceed with CRM integration test?')) {
            $this->info('Processing order with CRM integration...');
            
            try {
                $service = app(CrmOrderIntegrationService::class);
                $service->processConfirmedOrder($order);
                
                $this->info('✅ CRM integration completed successfully!');
                $this->newLine();
                
                // Show updated CRM state
                $this->showUpdatedCrmState($order);
                
            } catch (\Exception $e) {
                $this->error("❌ CRM integration failed: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    private function showAvailableOrders()
    {
        $this->info('Available orders for testing:');
        
        $orders = CustomOrder::whereIn('status', ['priced', 'confirmed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'customer_name', 'email', 'pickup_date', 'status', 'message_on_cake']);

        if ($orders->isEmpty()) {
            $this->warn('No suitable orders found for testing.');
            return;
        }

        $this->table(
            ['ID', 'Customer', 'Email', 'Pickup Date', 'Status', 'Message'],
            $orders->map(function ($order) {
                return [
                    $order->id,
                    $order->customer_name,
                    $order->email,
                    $order->pickup_date,
                    $order->status,
                    \Str::limit($order->message_on_cake ?? '', 30)
                ];
            })->toArray()
        );
    }

    private function showCurrentCrmState(CustomOrder $order)
    {
        $this->info('Current CRM State:');
        
        $customer = CrmCustomer::where('customer_id', strtolower($order->email))->first();
        
        if ($customer) {
            $this->line("✅ Customer exists in CRM: {$customer->buyer_name}");
            $this->line("   Orders Count: {$customer->orders_count}");
            $this->line("   Last Order: {$customer->last_order}");
            $this->line("   Favorite Flavors: {$customer->fav_flavors}");
            
            $occasions = $customer->occasions;
            $this->line("   Occasions: {$occasions->count()}");
            
            foreach ($occasions as $occasion) {
                $this->line("   - {$occasion->occasion_type}: {$occasion->history_count} orders, next: {$occasion->next_anchor_week_start}");
            }
        } else {
            $this->warn('❌ Customer not found in CRM (will be created)');
        }
        
        $this->newLine();
    }

    private function showUpdatedCrmState(CustomOrder $order)
    {
        $this->info('Updated CRM State:');
        
        $customer = CrmCustomer::where('customer_id', strtolower($order->email))->first();
        
        if ($customer) {
            $this->line("✅ Customer: {$customer->buyer_name}");
            $this->line("   Orders Count: {$customer->orders_count}");
            $this->line("   Last Order: {$customer->last_order}");
            $this->line("   Favorite Flavors: {$customer->fav_flavors}");
            
            $occasions = $customer->occasions;
            $this->line("   Occasions: {$occasions->count()}");
            
            foreach ($occasions as $occasion) {
                $this->line("   - {$occasion->occasion_type}: {$occasion->history_count} orders");
                $this->line("     Last Order: {$occasion->last_order_date_latest}");
                $this->line("     Next Anticipated: {$occasion->next_anticipated_order_date}");
                $this->line("     Anchor Week: {$occasion->anchor_week_start_date}");
                $this->line("     Reminder Date: {$occasion->reminder_date}");
            }
        }
    }

    private function createSampleOrder()
    {
        $this->info('Creating sample order for testing...');
        
        $order = CustomOrder::create([
            'customer_name' => 'John Doe',
            'email' => 'john.doe.test@example.com',
            'phone' => '+1234567890',
            'pickup_date' => now()->addDays(3),
            'pickup_time' => '14:00:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'cake_sponge' => 'Vanilla',
            'eggs_ok' => true,
            'message_on_cake' => 'Happy Birthday Sarah!',
            'custom_decoration' => 'Pink roses and butterflies',
            'allergies' => null,
            'sms_consent' => true,
            'status' => 'confirmed',
            'price' => 45.00,
        ]);

        $this->info("✅ Sample order created with ID: {$order->id}");
        $this->line("You can now test with: php artisan crm:test-integration {$order->id}");

        return Command::SUCCESS;
    }
}
