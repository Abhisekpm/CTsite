<?php
// IMPORTANT: Delete this file after running migrations!
// Only use this if you can't access SSH

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Running migrations...\n";

try {
    // Run the specific queue migrations
    $kernel->call('migrate', [
        '--path' => 'database/migrations/2025_08_20_195512_create_jobs_table.php'
    ]);
    
    $kernel->call('migrate', [
        '--path' => 'database/migrations/2025_08_20_195625_create_failed_jobs_table.php'
    ]);
    
    echo "Migrations completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR running migrations: " . $e->getMessage() . "\n";
}

// Verify tables were created
try {
    $jobsExists = \Illuminate\Support\Facades\Schema::hasTable('jobs');
    $failedExists = \Illuminate\Support\Facades\Schema::hasTable('failed_jobs');
    
    echo "Jobs table created: " . ($jobsExists ? 'YES' : 'NO') . "\n";
    echo "Failed jobs table created: " . ($failedExists ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "ERROR checking tables: " . $e->getMessage() . "\n";
}
?>