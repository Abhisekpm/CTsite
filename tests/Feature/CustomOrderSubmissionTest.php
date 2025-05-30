<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\CustomOrder;
use App\Models\CustomOrderImage;
use App\Models\Backend\Settings;
use App\Models\Backend\Testimonial;
use App\Models\Backend\MenuCategory;
use App\Models\Backend\Menu;
use Carbon\Carbon;

class CustomOrderSubmissionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        Storage::fake('public');
        Queue::fake();
        
        // Create necessary test data
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Create settings
        Settings::factory()->create();
        
        // Create testimonials
        Testimonial::factory()->count(3)->create(['status' => 'active']);
        
        // Create cake category and flavors
        $cakeCategory = MenuCategory::factory()->create(['slug' => 'cakes']);
        Menu::factory()->count(5)->create([
            'menu_category_id' => $cakeCategory->id
        ]);
    }

    public function test_custom_order_form_displays_correctly(): void
    {
        $response = $this->get(route('custom-order.create'));

        $response->assertStatus(200);
        $response->assertViewIs('custom_order');
        $response->assertViewHas(['settings', 'testimonials', 'cakeFlavors']);
        $response->assertSee('Custom Cake Order');
        $response->assertSee('Submit Order Request');
    }

    public function test_successful_order_submission_without_images(): void
    {
        $orderData = [
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
            'message_on_cake' => 'Happy Birthday!',
            'custom_decoration' => 'Pink roses with gold accents',
            'allergies' => null,
        ];

        $response = $this->post(route('custom-order.store'), $orderData);

        $response->assertRedirect(route('custom-order.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('custom_orders', [
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'status' => 'pending',
        ]);

        $order = CustomOrder::where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
    }

    public function test_successful_order_submission_with_images(): void
    {
        $files = [
            UploadedFile::fake()->image('design1.jpg', 800, 600),
            UploadedFile::fake()->image('design2.png', 1024, 768),
        ];

        $orderData = [
            'customer_name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+1987654321',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '16:30',
            'cake_size' => '10 inch',
            'cake_flavor' => 'Vanilla',
            'eggs_ok' => 'No',
            'decoration_images' => $files,
        ];

        $response = $this->post(route('custom-order.store'), $orderData);

        $response->assertRedirect(route('custom-order.create'));
        $response->assertSessionHas('success');

        $order = CustomOrder::where('email', 'jane@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(2, $order->images()->count());

        // Verify files were stored
        $images = $order->images;
        foreach ($images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }
    }

    public function test_validation_errors_for_required_fields(): void
    {
        $response = $this->post(route('custom-order.store'), []);

        $response->assertSessionHasErrors([
            'customer_name',
            'email',
            'phone',
            'pickup_date',
            'pickup_time',
            'cake_size',
            'cake_flavor',
            'eggs_ok',
        ]);
    }

    public function test_validation_errors_for_invalid_data(): void
    {
        $invalidData = [
            'customer_name' => '', // Required
            'email' => 'invalid-email', // Invalid email format
            'phone' => '123', // Too short
            'pickup_date' => Carbon::yesterday()->format('Y-m-d'), // Past date
            'pickup_time' => '25:00', // Invalid time
            'eggs_ok' => 'Maybe', // Invalid option
            'cake_size' => '',
            'cake_flavor' => '',
        ];

        $response = $this->post(route('custom-order.store'), $invalidData);

        $response->assertSessionHasErrors([
            'customer_name',
            'email',
            'pickup_date',
            'pickup_time',
            'eggs_ok',
            'cake_size',
            'cake_flavor',
        ]);
    }

    public function test_phone_number_normalization(): void
    {
        $orderData = [
            'customer_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '(555) 123-4567', // US format
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
        ];

        $response = $this->post(route('custom-order.store'), $orderData);

        $response->assertRedirect(route('custom-order.create'));

        $order = CustomOrder::where('email', 'test@example.com')->first();
        $this->assertNotNull($order);
        // Phone should be normalized to E.164 format
        $this->assertStringStartsWith('+1', $order->phone);
    }

    public function test_image_validation(): void
    {
        $invalidFiles = [
            UploadedFile::fake()->create('document.pdf', 1000), // Not an image
            UploadedFile::fake()->image('huge.jpg', 5000, 5000)->size(6000), // Too large
        ];

        $orderData = [
            'customer_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
            'decoration_images' => $invalidFiles,
        ];

        $response = $this->post(route('custom-order.store'), $orderData);

        $response->assertSessionHasErrors(['decoration_images.0', 'decoration_images.1']);
    }

    public function test_database_transaction_rollback_on_error(): void
    {
        // Mock a scenario where image saving fails
        Storage::shouldReceive('disk->put')
            ->andThrow(new \Exception('Storage error'));

        $orderData = [
            'customer_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
            'decoration_images' => [UploadedFile::fake()->image('test.jpg')],
        ];

        $response = $this->post(route('custom-order.store'), $orderData);

        // Order should not be created due to transaction rollback
        $this->assertDatabaseMissing('custom_orders', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('custom-order.create'));
        $response->assertSessionHas('error');
    }

    public function test_old_input_preserved_on_validation_error(): void
    {
        $incompleteData = [
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            // Missing required fields
        ];

        $response = $this->post(route('custom-order.store'), $incompleteData);

        $response->assertSessionHasInput('customer_name', 'John Doe');
        $response->assertSessionHasInput('email', 'john@example.com');
    }

    public function test_order_creation_with_optional_fields(): void
    {
        $orderData = [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
            'message_on_cake' => 'Happy Birthday!',
            'custom_decoration' => 'Pink theme',
            'allergies' => 'Nut allergy',
        ];

        $response = $this->post(route('custom-order.store'), $orderData);

        $response->assertRedirect(route('custom-order.create'));

        $this->assertDatabaseHas('custom_orders', [
            'email' => 'jane@example.com',
            'message_on_cake' => 'Happy Birthday!',
            'custom_decoration' => 'Pink theme',
            'allergies' => 'Nut allergy',
        ]);
    }

    public function test_multiple_orders_from_same_customer(): void
    {
        $baseData = [
            'customer_name' => 'Repeat Customer',
            'email' => 'repeat@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
        ];

        // Submit first order
        $this->post(route('custom-order.store'), $baseData);

        // Submit second order with different pickup date
        $secondOrderData = array_merge($baseData, [
            'pickup_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'cake_flavor' => 'Vanilla',
        ]);

        $response = $this->post(route('custom-order.store'), $secondOrderData);

        $response->assertRedirect(route('custom-order.create'));

        // Should have two orders from the same customer
        $this->assertEquals(2, CustomOrder::where('email', 'repeat@example.com')->count());
    }

    public function test_edge_case_pickup_date_today(): void
    {
        $orderData = [
            'customer_name' => 'Urgent Customer',
            'email' => 'urgent@example.com',
            'phone' => '+1234567890',
            'pickup_date' => Carbon::today()->format('Y-m-d'), // Today
            'pickup_time' => '14:00',
            'cake_size' => '6 inch',
            'cake_flavor' => 'Vanilla',
            'eggs_ok' => 'Yes',
        ];

        $response = $this->post(route('custom-order.store'), $orderData);

        $response->assertRedirect(route('custom-order.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('custom_orders', [
            'email' => 'urgent@example.com',
            'pickup_date' => Carbon::today()->format('Y-m-d'),
        ]);
    }
} 