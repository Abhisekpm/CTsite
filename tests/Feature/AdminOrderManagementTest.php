<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\CustomOrder;
use App\Models\CustomOrderImage;
use App\Models\User;
use Carbon\Carbon;

class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user (assuming you have user authentication)
        $this->adminUser = User::factory()->create(['role_name' => 'admin']);
    }

    public function test_admin_can_view_orders_index(): void
    {
        // Create test orders
        $orders = CustomOrder::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.index');
        $response->assertViewHas('orders');
        
        // Check that orders are displayed
        foreach ($orders as $order) {
            $response->assertSee($order->customer_name);
        }
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        // Create orders with different statuses
        $pendingOrders = CustomOrder::factory()->pending()->count(3)->create();
        $pricedOrders = CustomOrder::factory()->priced()->count(2)->create();
        $confirmedOrders = CustomOrder::factory()->confirmed()->count(2)->create();

        // Test pending filter
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index', ['status' => 'pending']));

        $response->assertStatus(200);
        foreach ($pendingOrders as $order) {
            $response->assertSee($order->customer_name);
        }

        // Test priced filter
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index', ['status' => 'priced']));

        $response->assertStatus(200);
        foreach ($pricedOrders as $order) {
            $response->assertSee($order->customer_name);
        }
    }

    public function test_admin_can_filter_orders_by_date(): void
    {
        // Create past and future orders
        $pastOrders = CustomOrder::factory()->pastPickup()->count(2)->create();
        $futureOrders = CustomOrder::factory()->futurePickup()->count(3)->create();

        // Test future orders (default)
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
        foreach ($futureOrders as $order) {
            $response->assertSee($order->customer_name);
        }

        // Test all time orders
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index', ['filter' => 'all_time']));

        $response->assertStatus(200);
        foreach ($pastOrders as $order) {
            $response->assertSee($order->customer_name);
        }
        foreach ($futureOrders as $order) {
            $response->assertSee($order->customer_name);
        }
    }

    public function test_admin_can_view_order_details(): void
    {
        $order = CustomOrder::factory()->create();
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.show');
        $response->assertViewHas('order');
        $response->assertSee($order->customer_name);
        $response->assertSee($order->email);
        $response->assertSee($order->phone);
    }

    public function test_admin_can_update_order_price(): void
    {
        $order = CustomOrder::factory()->pending()->create();
        
        $priceData = [
            'price' => 5000, // $50.00 in cents
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.orders.updatePrice', $order), $priceData);

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(5000, $order->price);
        $this->assertEquals('priced', $order->status);
    }

    public function test_admin_can_update_order_details(): void
    {
        $order = CustomOrder::factory()->create();
        
        $updateData = [
            'customer_name' => 'Updated Name',
            'cake_size' => '12 inch',
            'cake_flavor' => 'Red Velvet',
            'message_on_cake' => 'Updated Message',
            'allergies' => 'Updated allergies',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.orders.update', $order), $updateData);

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('Updated Name', $order->customer_name);
        $this->assertEquals('12 inch', $order->cake_size);
        $this->assertEquals('Red Velvet', $order->cake_flavor);
    }

    public function test_admin_can_confirm_priced_order(): void
    {
        $order = CustomOrder::factory()->priced()->create();
        
        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.orders.confirm', $order));

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('confirmed', $order->status);
    }

    public function test_admin_cannot_confirm_unprice_order(): void
    {
        $order = CustomOrder::factory()->pending()->create();
        
        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.orders.confirm', $order));

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }

    public function test_admin_can_cancel_order(): void
    {
        $order = CustomOrder::factory()->pending()->create();
        
        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.orders.cancel', $order));

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_admin_cannot_cancel_confirmed_order(): void
    {
        $order = CustomOrder::factory()->confirmed()->create();
        
        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.orders.cancel', $order));

        $response->assertRedirect(route('admin.orders.show', $order));
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals('confirmed', $order->status);
    }

    public function test_admin_can_view_todays_dispatch(): void
    {
        // Create orders for today and other days
        $todaysOrders = CustomOrder::factory()->confirmed()->todayPickup()->count(2)->create();
        $otherDayOrders = CustomOrder::factory()->confirmed()->futurePickup()->count(3)->create();
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.printTodaysDispatch'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.print_dispatch');
        $response->assertViewHas('orders');
        
        // Should see today's orders
        foreach ($todaysOrders as $order) {
            $response->assertSee($order->customer_name);
        }
    }

    public function test_admin_can_view_dispatch_for_specific_date(): void
    {
        $specificDate = Carbon::tomorrow();
        $ordersForDate = CustomOrder::factory()->confirmed()->count(2)->create([
            'pickup_date' => $specificDate->format('Y-m-d')
        ]);
        $otherDayOrders = CustomOrder::factory()->confirmed()->futurePickup()->count(2)->create();
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.printDispatchForDate', $specificDate->format('Y-m-d')));

        $response->assertStatus(200);
        $response->assertViewIs('admin.orders.print_dispatch');
        $response->assertViewHas('orders');
        
        // Should only see orders for the specific date
        foreach ($ordersForDate as $order) {
            $response->assertSee($order->customer_name);
        }
    }

    public function test_admin_dispatch_date_validation(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.printDispatchForDate', 'invalid-date'));

        $response->assertRedirect(route('admin.orders.index'));
        $response->assertSessionHas('error');
    }

    public function test_order_pagination(): void
    {
        // Create more than 20 orders to test pagination
        CustomOrder::factory()->count(25)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Next'); // Pagination link
    }

    public function test_order_search_preserves_filters(): void
    {
        CustomOrder::factory()->pending()->count(15)->create();
        CustomOrder::factory()->priced()->count(10)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index', ['status' => 'pending', 'page' => 2]));

        $response->assertStatus(200);
        // Check that pagination links preserve the status filter
        $response->assertSee('status=pending');
    }

    public function test_order_validation_on_update(): void
    {
        $order = CustomOrder::factory()->create();
        
        $invalidData = [
            'customer_name' => '', // Required field
            'pickup_date' => 'invalid-date',
            'eggs_ok' => 'Invalid',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.orders.update', $order), $invalidData);

        $response->assertSessionHasErrors(['customer_name', 'pickup_date', 'eggs_ok']);
    }

    public function test_price_validation_on_update(): void
    {
        $order = CustomOrder::factory()->pending()->create();
        
        $invalidPriceData = [
            'price' => -100, // Negative price
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.orders.updatePrice', $order), $invalidPriceData);

        $response->assertSessionHasErrors(['price']);
    }

    public function test_admin_can_view_order_images(): void
    {
        $order = CustomOrder::factory()->create();
        $images = CustomOrderImage::factory()->count(3)->forOrder($order)->create();
        
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        
        // Check that image paths are displayed or referenced
        foreach ($images as $image) {
            $response->assertSee($image->path);
        }
    }

    public function test_orders_are_sorted_by_pickup_date(): void
    {
        // Create orders with different pickup dates
        $order1 = CustomOrder::factory()->create(['pickup_date' => Carbon::now()->addDays(3)]);
        $order2 = CustomOrder::factory()->create(['pickup_date' => Carbon::now()->addDays(1)]);
        $order3 = CustomOrder::factory()->create(['pickup_date' => Carbon::now()->addDays(2)]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
        
        // Extract the order of customer names from the response
        $content = $response->getContent();
        $order2Pos = strpos($content, $order2->customer_name);
        $order3Pos = strpos($content, $order3->customer_name);
        $order1Pos = strpos($content, $order1->customer_name);

        // Assert that orders appear in chronological order by pickup date
        $this->assertLessThan($order3Pos, $order2Pos);
        $this->assertLessThan($order1Pos, $order3Pos);
    }
} 