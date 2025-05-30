<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\OrderController;
use App\Models\CustomOrder;
use App\Models\Backend\Settings;
use App\Models\Backend\Testimonial;
use App\Models\Backend\MenuCategory;
use App\Models\Backend\Menu;
use Mockery;
use Carbon\Carbon;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data needed for controller
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

    public function test_create_method_returns_correct_view(): void
    {
        $controller = new OrderController();
        $response = $controller->create();

        $this->assertEquals('custom_order', $response->getName());
        $this->assertArrayHasKey('settings', $response->getData());
        $this->assertArrayHasKey('testimonials', $response->getData());
        $this->assertArrayHasKey('cakeFlavors', $response->getData());
    }

    public function test_create_method_handles_missing_cake_category(): void
    {
        // Create test data without cake category
        Settings::factory()->create();
        Testimonial::factory()->count(3)->create(['status' => 'active']);
        
        $response = $this->controller->create();

        $this->assertEquals('custom_order', $response->getName());
        $viewData = $response->getData();
        $this->assertArrayHasKey('cakeFlavors', $viewData);
        $this->assertCount(0, $viewData['cakeFlavors']); // Should be empty collection
    }

    public function test_store_method_validation_rules(): void
    {
        $request = Request::create('/', 'POST', [
            'customer_name' => '',
            'email' => 'invalid-email',
            'phone' => '123',
            'pickup_date' => 'invalid-date',
            'pickup_time' => '25:00',
            'eggs_ok' => 'Maybe',
            'cake_size' => '',
            'cake_flavor' => '',
        ]);

        $this->expectException(ValidationException::class);
        
        $response = $this->controller->store($request);
    }

    public function test_phone_number_validation(): void
    {
        $phoneValidationRule = 'required|string|min:10|max:20';
        
        // Valid phone numbers
        $validPhones = [
            '+1234567890',
            '(555) 123-4567',
            '555-123-4567',
            '5551234567',
            '+44 20 7946 0958',
        ];

        foreach ($validPhones as $phone) {
            $validator = Validator::make(['phone' => $phone], ['phone' => $phoneValidationRule]);
            $this->assertFalse($validator->fails(), "Valid phone number {$phone} failed validation");
        }

        // Invalid phone numbers
        $invalidPhones = [
            '123',
            '',
            '12345678901234567890123', // Too long
            'abc',
        ];

        foreach ($invalidPhones as $phone) {
            $validator = Validator::make(['phone' => $phone], ['phone' => $phoneValidationRule]);
            $this->assertTrue($validator->fails(), "Invalid phone number {$phone} passed validation");
        }
    }

    public function test_pickup_date_validation(): void
    {
        $dateValidationRule = 'required|date|after_or_equal:today';
        
        // Valid dates (today and future)
        $validDates = [
            Carbon::today()->format('Y-m-d'),
            Carbon::tomorrow()->format('Y-m-d'),
            Carbon::now()->addWeek()->format('Y-m-d'),
        ];

        foreach ($validDates as $date) {
            $validator = Validator::make(['pickup_date' => $date], ['pickup_date' => $dateValidationRule]);
            $this->assertFalse($validator->fails(), "Valid date {$date} failed validation");
        }

        // Invalid dates (past)
        $invalidDates = [
            Carbon::yesterday()->format('Y-m-d'),
            Carbon::now()->subWeek()->format('Y-m-d'),
            '2020-01-01',
        ];

        foreach ($invalidDates as $date) {
            $validator = Validator::make(['pickup_date' => $date], ['pickup_date' => $dateValidationRule]);
            $this->assertTrue($validator->fails(), "Invalid date {$date} passed validation");
        }
    }

    public function test_eggs_ok_validation(): void
    {
        $eggsValidationRule = 'required|in:Yes,No';
        
        // Valid values
        foreach (['Yes', 'No'] as $validValue) {
            $validator = Validator::make(['eggs_ok' => $validValue], ['eggs_ok' => $eggsValidationRule]);
            $this->assertFalse($validator->fails(), "Valid eggs_ok value '{$validValue}' failed validation");
        }

        // Invalid values
        $invalidValues = ['Maybe', 'yes', 'no', '', '1', '0'];
        
        foreach ($invalidValues as $invalidValue) {
            $validator = Validator::make(['eggs_ok' => $invalidValue], ['eggs_ok' => $eggsValidationRule]);
            $this->assertTrue($validator->fails(), "Invalid eggs_ok value '{$invalidValue}' passed validation");
        }
    }

    public function test_image_validation_rules(): void
    {
        Storage::fake('public');

        // Test valid images
        $validImages = [
            UploadedFile::fake()->image('test.jpg', 800, 600),
            UploadedFile::fake()->image('test.png', 1024, 768),
            UploadedFile::fake()->image('test.gif', 500, 500),
        ];

        $request = Request::create('/', 'POST', [
            'customer_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
        ]);

        $request->files->set('decoration_images', $validImages);

        try {
            $response = $this->controller->store($request);
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('Valid images failed validation');
        }
    }

    public function test_image_size_validation(): void
    {
        Storage::fake('public');

        // Test oversized image (over 5MB)
        $oversizedImage = UploadedFile::fake()->image('huge.jpg', 5000, 5000)->size(6000); // 6MB

        $request = Request::create('/', 'POST', [
            'customer_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'pickup_time' => '14:00',
            'cake_size' => '8 inch',
            'cake_flavor' => 'Chocolate',
            'eggs_ok' => 'Yes',
        ]);

        $request->files->set('decoration_images', [$oversizedImage]);

        $this->expectException(ValidationException::class);
        $response = $this->controller->store($request);
    }

    public function test_optional_fields_validation(): void
    {
        $optionalFieldsRules = [
            'message_on_cake' => 'nullable|string|max:255',
            'custom_decoration' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:500',
        ];

        // Test with null/empty values (should pass)
        $emptyData = [
            'message_on_cake' => null,
            'custom_decoration' => '',
            'allergies' => null,
        ];

        $validator = Validator::make($emptyData, $optionalFieldsRules);
        $this->assertFalse($validator->fails());

        // Test with valid values
        $validData = [
            'message_on_cake' => 'Happy Birthday!',
            'custom_decoration' => 'Pink roses with gold accents',
            'allergies' => 'Nut allergy',
        ];

        $validator = Validator::make($validData, $optionalFieldsRules);
        $this->assertFalse($validator->fails());

        // Test with too long values (should fail)
        $tooLongData = [
            'message_on_cake' => str_repeat('a', 256), // Too long
            'custom_decoration' => str_repeat('a', 1001), // Too long
            'allergies' => str_repeat('a', 501), // Too long
        ];

        $validator = Validator::make($tooLongData, $optionalFieldsRules);
        $this->assertTrue($validator->fails());
    }

    public function test_time_format_validation(): void
    {
        $timeValidationRule = 'required|date_format:H:i';
        
        // Valid time formats
        $validTimes = ['14:00', '09:30', '23:59', '00:00'];
        
        foreach ($validTimes as $time) {
            $validator = Validator::make(['pickup_time' => $time], ['pickup_time' => $timeValidationRule]);
            $this->assertFalse($validator->fails(), "Valid time {$time} failed validation");
        }

        // Invalid time formats
        $invalidTimes = ['25:00', '14:60', '2:30', '14:30:00', 'invalid'];
        
        foreach ($invalidTimes as $time) {
            $validator = Validator::make(['pickup_time' => $time], ['pickup_time' => $timeValidationRule]);
            $this->assertTrue($validator->fails(), "Invalid time {$time} passed validation");
        }
    }
} 