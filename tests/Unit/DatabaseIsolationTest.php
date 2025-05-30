<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\Backend\Settings;

class DatabaseIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_using_testing_database(): void
    {
        // This test verifies we're using the testing database (SQLite in memory)
        $databaseName = DB::connection()->getDatabaseName();
        
        // For SQLite in memory, this should be ':memory:' or empty
        $this->assertTrue(
            $databaseName === ':memory:' || empty($databaseName),
            "Expected testing database (:memory:), but got: {$databaseName}"
        );
    }

    public function test_can_create_settings_without_affecting_main_db(): void
    {
        // Create a test settings record
        $settings = Settings::create([
            'website_name' => 'Test Website',
            'logo' => 'test-logo.png',
            'favicon' => 'test-favicon.png',
            'meta_title' => 'Test Meta Title',
            'meta_keywords' => 'test, keywords',
            'meta_description' => 'Test meta description',
        ]);

        $this->assertNotNull($settings);
        $this->assertEquals('Test Website', $settings->website_name);
        
        // This test data should only exist in the test database
        // and not affect the main production database
    }
} 