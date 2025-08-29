# Production Migration Guide - Clean Occasions Data

## Method 1: Laravel Artisan Command (Recommended)

### Step 1: Upload Files to Production
```bash
# Upload the clean CSV and import command to production
scp CRM/extracted_occasions.csv user@yourserver.com:/path/to/production/CRM/
scp app/Console/Commands/ImportExtractedOccasions.php user@yourserver.com:/path/to/production/app/Console/Commands/
```

### Step 2: Run Import on Production
```bash
# SSH into production server
ssh user@yourserver.com

# Navigate to Laravel root
cd /path/to/production

# Run the import command
php artisan crm:import-extracted-occasions
```

## Method 2: Direct Database Export/Import (Fastest)

### Step 1: Export Clean Data from Dev
```bash
# Export only the occasions table from dev database
php artisan tinker --execute="
use App\Models\CrmOccasion;
\$occasions = CrmOccasion::all();
file_put_contents('CRM/occasions_export.sql', '');
foreach(\$occasions as \$occasion) {
    \$sql = 'INSERT INTO crm_occasions (customer_id, occasion_type, honoree_name, anchor_confidence, last_order_date_latest, history_count, history_years, anchor_week_start_date, next_anticipated_order_date, reminder_date, reminder_sent, notes, created_at, updated_at) VALUES (' .
    '\"' . addslashes(\$occasion->customer_id) . '\", ' .
    '\"' . addslashes(\$occasion->occasion_type) . '\", ' .
    (\$occasion->honoree_name ? '\"' . addslashes(\$occasion->honoree_name) . '\"' : 'NULL') . ', ' .
    '\"' . \$occasion->anchor_confidence . '\", ' .
    (\$occasion->last_order_date_latest ? '\"' . \$occasion->last_order_date_latest . '\"' : 'NULL') . ', ' .
    \$occasion->history_count . ', ' .
    (\$occasion->history_years ? '\"' . addslashes(\$occasion->history_years) . '\"' : 'NULL') . ', ' .
    (\$occasion->anchor_week_start_date ? '\"' . \$occasion->anchor_week_start_date . '\"' : 'NULL') . ', ' .
    (\$occasion->next_anticipated_order_date ? '\"' . \$occasion->next_anticipated_order_date . '\"' : 'NULL') . ', ' .
    (\$occasion->reminder_date ? '\"' . \$occasion->reminder_date . '\"' : 'NULL') . ', ' .
    '0, ' .
    (\$occasion->notes ? '\"' . addslashes(\$occasion->notes) . '\"' : 'NULL') . ', ' .
    '\"' . \$occasion->created_at . '\", ' .
    '\"' . \$occasion->updated_at . '\"' .
    ');' . PHP_EOL;
    file_put_contents('CRM/occasions_export.sql', \$sql, FILE_APPEND);
}
echo 'SQL export completed: CRM/occasions_export.sql' . PHP_EOL;
"
```

### Step 2: Upload and Import to Production
```bash
# Upload SQL file
scp CRM/occasions_export.sql user@yourserver.com:/path/to/production/

# SSH into production
ssh user@yourserver.com

# Import via MySQL
mysql -u username -p database_name < occasions_export.sql
```

## Method 3: phpMyAdmin/cPanel Database (GUI Method)

### Step 1: Export from Dev Database
```bash
# Generate MySQL dump
mysqldump -u username -p --no-create-info --complete-insert --where="1 limit 10000" database_name crm_occasions > occasions_dump.sql
```

### Step 2: Upload via cPanel
1. Login to cPanel
2. Go to phpMyAdmin
3. Select production database
4. Clear existing occasions: `TRUNCATE TABLE crm_occasions;`
5. Import the SQL file

## Method 4: Custom Production Seeder (Most Reliable)

### Step 1: Create Production Seeder
Create `database/seeders/ProductionOccasionsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CrmOccasion;
use Carbon\Carbon;

class ProductionOccasionsSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Clearing existing occasions...');
        CrmOccasion::truncate();
        
        $this->command->info('Importing clean occasions data...');
        
        $csvPath = base_path('CRM/extracted_occasions.csv');
        $handle = fopen($csvPath, 'r');
        fgetcsv($handle); // Skip header
        
        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0]) || empty($row[3])) continue;
            
            CrmOccasion::create([
                'customer_id' => $row[0],
                'occasion_type' => $row[3],
                'honoree_name' => $row[4] ?: null,
                'anchor_confidence' => $row[5] ?: 'medium',
                'last_order_date_latest' => $row[6] ? Carbon::parse($row[6]) : null,
                'history_count' => (int)($row[7] ?: 1),
                'history_years' => $row[8] ?: null,
                'anchor_week_start_date' => $row[9] ? Carbon::parse($row[9]) : null,
                'next_anticipated_order_date' => $row[10] ? Carbon::parse($row[10]) : null,
                'reminder_date' => $row[11] ? Carbon::parse($row[11]) : null,
                'reminder_sent' => false,
                'notes' => $row[13] ?: null,
            ]);
            $imported++;
            
            if ($imported % 100 == 0) {
                $this->command->info("Imported {$imported} occasions...");
            }
        }
        
        fclose($handle);
        $this->command->info("Successfully imported {$imported} occasions!");
    }
}
```

### Step 2: Run on Production
```bash
# Upload seeder and CSV
scp database/seeders/ProductionOccasionsSeeder.php user@server:/path/to/prod/database/seeders/
scp CRM/extracted_occasions.csv user@server:/path/to/prod/CRM/

# SSH and run
ssh user@server
cd /path/to/production
php artisan db:seed --class=ProductionOccasionsSeeder
```

## Method 5: One-Command Solution (Recommended for GoDaddy)

Create a single deployment script:

```bash
#!/bin/bash
# File: deploy_occasions.sh

echo "🚀 Deploying clean occasions to production..."

# Upload files
scp CRM/extracted_occasions.csv user@server:/path/to/prod/CRM/
scp app/Console/Commands/ImportExtractedOccasions.php user@server:/path/to/prod/app/Console/Commands/

# Run import on production
ssh user@server << 'EOF'
cd /path/to/production
php artisan crm:import-extracted-occasions
echo "✅ Production deployment completed!"
EOF

echo "🎉 Clean occasions data deployed to production!"
```

## Pre-Migration Checklist

Before migrating:

1. **Backup Production Database**
```bash
ssh user@server
mysqldump -u username -p database_name > backup_before_occasions_$(date +%Y%m%d).sql
```

2. **Test Connection**
```bash
php artisan tinker --execute="echo 'DB Connection: ' . App\Models\CrmOccasion::count() . ' occasions' . PHP_EOL;"
```

3. **Verify Customer Links**
```bash
php artisan tinker --execute="echo 'Customers: ' . App\Models\CrmCustomer::count() . PHP_EOL;"
```

## Recommended Approach for GoDaddy

**Use Method 1 (Laravel Artisan Command)** because:
- ✅ Uses existing tested import logic
- ✅ Provides progress feedback
- ✅ Handles errors gracefully  
- ✅ Works reliably with cPanel/SSH
- ✅ Maintains data integrity

## Verification Commands (Run After Migration)

```bash
# Check total count
php artisan tinker --execute="echo 'Total occasions: ' . App\Models\CrmOccasion::count() . PHP_EOL;"

# Check data quality
php artisan tinker --execute="
echo 'Birthdays: ' . App\Models\CrmOccasion::where('occasion_type', 'birthday')->count() . PHP_EOL;
echo 'Future dates: ' . App\Models\CrmOccasion::where('next_anticipated_order_date', '>', now())->count() . PHP_EOL;
echo 'With honorees: ' . App\Models\CrmOccasion::whereNotNull('honoree_name')->count() . PHP_EOL;
"
```

Which method would you prefer to use for your GoDaddy production deployment?