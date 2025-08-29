# Database Migration Commands

## Step 1: Clear Existing Occasions Database

### Option A: Using Laravel Tinker (Recommended)
```bash
php artisan tinker --execute="
use App\Models\CrmOccasion;
echo 'Current occasions count: ' . CrmOccasion::count() . PHP_EOL;
CrmOccasion::truncate();
echo 'Occasions table cleared successfully!' . PHP_EOL;
"
```

### Option B: Using Raw SQL (Alternative)
```bash
php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
DB::table('crm_occasions')->truncate();
echo 'Occasions table truncated successfully!' . PHP_EOL;
"
```

## Step 2: Import Extracted Occasions

### Method 1: Create Laravel Import Command (Recommended)

Create a new Artisan command:
```bash
php artisan make:command ImportExtractedOccasions
```

Then edit `app/Console/Commands/ImportExtractedOccasions.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrmOccasion;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ImportExtractedOccasions extends Command
{
    protected $signature = 'crm:import-occasions {file=CRM/extracted_occasions.xlsx}';
    protected $description = 'Import extracted occasions from Excel file';

    public function handle()
    {
        $filePath = base_path($this->argument('file'));
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Reading Excel file: {$filePath}");
        
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getSheet(0); // First sheet
        $rows = $worksheet->toArray();
        
        // Remove header row
        $header = array_shift($rows);
        
        $imported = 0;
        $errors = 0;
        
        foreach ($rows as $row) {
            try {
                CrmOccasion::create([
                    'customer_id' => $row[0],
                    'occasion_type' => $row[3],
                    'honoree_name' => $row[4] ?: null,
                    'anchor_confidence' => $row[5],
                    'last_order_date_latest' => $row[6] ? Carbon::parse($row[6]) : null,
                    'history_count' => (int)$row[7],
                    'history_years' => $row[8] ?: null,
                    'anchor_week_start_date' => $row[9] ? Carbon::parse($row[9]) : null,
                    'next_anticipated_order_date' => $row[10] ? Carbon::parse($row[10]) : null,
                    'reminder_date' => $row[11] ? Carbon::parse($row[11]) : null,
                    'reminder_sent' => (bool)$row[12],
                    'notes' => $row[13] ?: null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $this->warn("Error importing row: " . $e->getMessage());
                $errors++;
            }
        }
        
        $this->info("Import completed!");
        $this->info("Imported: {$imported} occasions");
        $this->warn("Errors: {$errors}");
        
        return 0;
    }
}
```

### Method 2: Direct Database Import using CSV

First, convert Excel to CSV:
```bash
# You'll need to save the Excel file as CSV manually or use a converter
```

Then import via MySQL:
```sql
LOAD DATA LOCAL INFILE 'C:/Users/abhis/CTsite/CRM/extracted_occasions.csv'
INTO TABLE crm_occasions
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(customer_id, customer_email, customer_name, occasion_type, honoree_name, anchor_confidence, last_order_date_latest, history_count, history_years, anchor_week_start_date, next_anticipated_order_date, reminder_date, reminder_sent, notes, created_at, updated_at);
```

## Step 3: Quick Import Script (Easiest Method)

Create a simple PHP script:

```php
<?php
// File: CRM/import_occasions.php
require_once '../vendor/autoload.php';
require_once '../bootstrap/app.php';

use App\Models\CrmOccasion;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

echo "Starting occasion import...\n";

// Clear existing data
CrmOccasion::truncate();
echo "Cleared existing occasions\n";

// Load Excel file
$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load('extracted_occasions.xlsx');
$worksheet = $spreadsheet->getActiveSheet();
$data = $worksheet->toArray();

// Remove header
$header = array_shift($data);

$count = 0;
foreach ($data as $row) {
    try {
        CrmOccasion::create([
            'customer_id' => $row[0],
            'occasion_type' => $row[3],
            'honoree_name' => $row[4] ?: null,
            'anchor_confidence' => $row[5],
            'last_order_date_latest' => $row[6] ? Carbon::parse($row[6]) : null,
            'history_count' => (int)$row[7],
            'history_years' => $row[8] ?: null,
            'anchor_week_start_date' => $row[9] ? Carbon::parse($row[9]) : null,
            'next_anticipated_order_date' => $row[10] ? Carbon::parse($row[10]) : null,
            'reminder_date' => $row[11] ? Carbon::parse($row[11]) : null,
            'reminder_sent' => false,
            'notes' => $row[13] ?: null,
        ]);
        $count++;
        if ($count % 100 == 0) {
            echo "Imported {$count} occasions...\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Import completed! Total: {$count} occasions\n";
?>
```

Run it with:
```bash
cd CRM
php import_occasions.php
```

## Step 4: Verification Commands

After import, verify the data:

```bash
php artisan tinker --execute="
use App\Models\CrmOccasion;
echo 'Total occasions: ' . CrmOccasion::count() . PHP_EOL;
echo 'Birthday occasions: ' . CrmOccasion::where('occasion_type', 'birthday')->count() . PHP_EOL;
echo 'Anniversary occasions: ' . CrmOccasion::where('occasion_type', 'anniversary')->count() . PHP_EOL;
echo 'High confidence: ' . CrmOccasion::where('anchor_confidence', 'high')->count() . PHP_EOL;
echo 'Future dates: ' . CrmOccasion::where('next_anticipated_order_date', '>', now())->count() . PHP_EOL;
"
```

## Recommended Approach

1. **Use Method 3 (Quick Import Script)** - It's the simplest and most reliable
2. First install PhpSpreadsheet if not already installed:
   ```bash
   composer require phpoffice/phpspreadsheet
   ```
3. Run the import script
4. Verify the results
5. Test the CRM admin pages to ensure everything works

## Rollback Plan

If something goes wrong, you can restore from backup:
```bash
php artisan tinker --execute="
// If you have a backup, restore it here
// Or re-run the original seeders
"
```