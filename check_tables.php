<?php
// Upload this to your production server to check tables

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking queue tables...\n";

try {
    // Check if jobs table exists
    $jobsExists = \Illuminate\Support\Facades\Schema::hasTable('jobs');
    echo "Jobs table exists: " . ($jobsExists ? 'YES' : 'NO') . "\n";
    
    // Check if failed_jobs table exists  
    $failedExists = \Illuminate\Support\Facades\Schema::hasTable('failed_jobs');
    echo "Failed jobs table exists: " . ($failedExists ? 'YES' : 'NO') . "\n";
    
    if ($jobsExists) {
        $jobCount = \Illuminate\Support\Facades\DB::table('jobs')->count();
        echo "Jobs in queue: {$jobCount}\n";
    }
    
    if ($failedExists) {
        $failedCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        echo "Failed jobs: {$failedCount}\n";
    }
    
    // Test database connection
    echo "Database connection: OK\n";
    echo "Queue connection config: " . config('queue.default') . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>