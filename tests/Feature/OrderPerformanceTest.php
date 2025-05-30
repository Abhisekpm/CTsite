<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\CustomOrder;
use App\Models\CustomOrderImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class OrderPerformanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->adminUser = User::factory()->create(['role_name' => 'admin']);
        Storage::fake('public');
        Queue::fake();
    }

    public function test_can_handle_bulk_order_creation(): void
    {
        $startTime = microtime(true);
        
        // Create 100 orders to test bulk performance
        $orders = CustomOrder::factory()->count(100)->create();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertCount(100, $orders);
        $this->assertLessThan(5.0, $executionTime, 'Bulk order creation took too long'); // Should complete in under 5 seconds
    }

    public function test_admin_orders_index_performance_with_large_dataset(): void
    {
        // Create a large dataset
        CustomOrder::factory()->count(1000)->create();
        
        $startTime = microtime(true);
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index'));
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(2.0, $executionTime, 'Orders index page took too long to load'); // Should load in under 2 seconds
    }

    public function test_pagination_performance(): void
    {
        // Create enough orders to trigger pagination
        CustomOrder::factory()->count(100)->create();
        
        $startTime = microtime(true);
        
        // Test multiple pagination pages
        for ($page = 1; $page <= 3; $page++) {
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.orders.index', ['page' => $page]));
            
            $response->assertStatus(200);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(3.0, $executionTime, 'Pagination requests took too long');
    }

    public function test_filtered_orders_performance(): void
    {
        // Create orders with different statuses
        CustomOrder::factory()->pending()->count(250)->create();
        CustomOrder::factory()->priced()->count(250)->create();
        CustomOrder::factory()->confirmed()->count(250)->create();
        CustomOrder::factory()->cancelled()->count(250)->create();
        
        $startTime = microtime(true);
        
        // Test filtering by each status
        $statuses = ['pending', 'priced', 'confirmed', 'cancelled'];
        
        foreach ($statuses as $status) {
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.orders.index', ['status' => $status]));
            
            $response->assertStatus(200);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(4.0, $executionTime, 'Status filtering took too long');
    }

    public function test_concurrent_order_submissions(): void
    {
        $concurrentSubmissions = 10;
        $results = [];
        
        $startTime = microtime(true);
        
        // Simulate concurrent order submissions
        for ($i = 0; $i < $concurrentSubmissions; $i++) {
            $orderData = [
                'customer_name' => "Customer {$i}",
                'email' => "customer{$i}@example.com",
                'phone' => "+123456789{$i}",
                'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
                'pickup_time' => '14:00',
                'cake_size' => '8 inch',
                'cake_flavor' => 'Chocolate',
                'eggs_ok' => 'Yes',
            ];
            
            $response = $this->post(route('custom-order.store'), $orderData);
            $results[] = $response->getStatusCode();
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // All submissions should succeed
        foreach ($results as $statusCode) {
            $this->assertEquals(302, $statusCode); // Redirect after successful submission
        }
        
        // Should complete all submissions in reasonable time
        $this->assertLessThan(10.0, $executionTime, 'Concurrent submissions took too long');
        
        // Verify all orders were created
        $this->assertEquals($concurrentSubmissions, CustomOrder::count());
    }

    public function test_large_image_upload_performance(): void
    {
        $images = [
            UploadedFile::fake()->image('large1.jpg', 2000, 2000)->size(4000), // 4MB
            UploadedFile::fake()->image('large2.jpg', 2000, 2000)->size(4000), // 4MB
            UploadedFile::fake()->image('large3.jpg', 2000, 2000)->size(4000), // 4MB
        ];
        
        $orderData = [
            'customer_name' => 'Performance Test',
            'email' => 'performance@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
            'decoration_images' => $images,
        ];
        
        $startTime = microtime(true);
        
        $response = $this->post(route('custom-order.store'), $orderData);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $response->assertRedirect(route('custom-order.create'));
        $this->assertLessThan(15.0, $executionTime, 'Large image upload took too long');
        
        // Verify order and images were created
        $order = CustomOrder::where('email', 'performance@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->images()->count());
    }

    public function test_database_transaction_performance(): void
    {
        $orderData = [
            'customer_name' => 'Transaction Test',
            'email' => 'transaction@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
            'decoration_images' => [
                UploadedFile::fake()->image('test1.jpg'),
                UploadedFile::fake()->image('test2.jpg'),
            ],
        ];
        
        // Measure database queries
        DB::enableQueryLog();
        
        $startTime = microtime(true);
        
        $response = $this->post(route('custom-order.store'), $orderData);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        $response->assertRedirect(route('custom-order.create'));
        
        // Should complete transaction in reasonable time
        $this->assertLessThan(5.0, $executionTime, 'Database transaction took too long');
        
        // Should not have excessive queries
        $this->assertLessThan(20, count($queries), 'Too many database queries executed');
    }

    public function test_memory_usage_with_large_datasets(): void
    {
        $initialMemory = memory_get_usage();
        
        // Create a large dataset
        CustomOrder::factory()->count(500)->create();
        
        // Load the admin orders page
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index'));
        
        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $initialMemory;
        
        $response->assertStatus(200);
        
        // Should not use excessive memory (limit to 50MB)
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Memory usage too high');
    }

    public function test_search_and_filter_performance_combination(): void
    {
        // Create diverse dataset
        CustomOrder::factory()->pending()->futurePickup()->count(100)->create();
        CustomOrder::factory()->priced()->futurePickup()->count(100)->create();
        CustomOrder::factory()->confirmed()->todayPickup()->count(50)->create();
        CustomOrder::factory()->confirmed()->pastPickup()->count(150)->create();
        
        $startTime = microtime(true);
        
        // Test various filter combinations
        $filterCombinations = [
            ['status' => 'pending'],
            ['status' => 'confirmed'],
            ['filter' => 'all_time'],
            ['status' => 'priced', 'filter' => 'all_time'],
        ];
        
        foreach ($filterCombinations as $filters) {
            $response = $this->actingAs($this->adminUser)
                ->get(route('admin.orders.index', $filters));
            
            $response->assertStatus(200);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(5.0, $executionTime, 'Combined search and filter operations took too long');
    }

    public function test_order_update_performance(): void
    {
        $orders = CustomOrder::factory()->count(10)->create();
        
        $startTime = microtime(true);
        
        // Update multiple orders
        foreach ($orders as $order) {
            $updateData = [
                'customer_name' => 'Updated ' . $order->customer_name,
                'cake_flavor' => 'Updated Flavor',
            ];
            
            $response = $this->actingAs($this->adminUser)
                ->put(route('admin.orders.update', $order), $updateData);
            
            $response->assertRedirect();
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $this->assertLessThan(3.0, $executionTime, 'Order updates took too long');
    }

    public function test_dispatch_report_generation_performance(): void
    {
        // Create orders for today
        CustomOrder::factory()->confirmed()->todayPickup()->count(50)->create();
        
        $startTime = microtime(true);
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.printTodaysDispatch'));
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(2.0, $executionTime, 'Dispatch report generation took too long');
    }

    public function test_stress_test_rapid_form_submissions(): void
    {
        $submissions = 20;
        $successCount = 0;
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $submissions; $i++) {
            $orderData = [
                'customer_name' => "Stress Test Customer {$i}",
                'email' => "stress{$i}@example.com",
                'phone' => "+1234567" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
                'pickup_time' => '14:00',
                'cake_size' => '8 inch',
                'cake_flavor' => 'Vanilla',
                'eggs_ok' => 'Yes',
            ];
            
            $response = $this->post(route('custom-order.store'), $orderData);
            
            if ($response->getStatusCode() === 302) {
                $successCount++;
            }
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // All submissions should succeed
        $this->assertEquals($submissions, $successCount);
        
        // Should handle rapid submissions
        $this->assertLessThan(15.0, $executionTime, 'Stress test took too long');
        
        // Verify all orders were created correctly
        $this->assertEquals($submissions, CustomOrder::count());
    }
} 