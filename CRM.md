# CRM System Implementation Plan

## Overview

This document outlines the integration of a comprehensive Customer Relationship Management (CRM) system into the existing Laravel bakery application. The CRM will be built around two core data entities derived from the CSV analysis: **Customers** and **Occasions**.

## Data Analysis Summary

### Customers Master Data Structure
Based on `customers_master.csv` analysis (10,000+ records):
- **Identity**: customer_id (email-based), buyer_name, primary_phone, primary_email
- **Order History**: first_order, last_order, orders_count
- **Preferences**: fav_flavors, eggs_ok, allergens
- **Marketing**: marketing_opt_in, channel_preference
- **Notes**: Custom notes field

### Occasions Master Data Structure  
Based on `occasions_master.csv` analysis (50,000+ records) with **Weekly Grouping Approach**:
- **Event Details**: occasion_type (birthday/anniversary/other), honoree_name
- **Weekly Timing**: anchor_week_start_date (Monday start), anchor_window_days (flexibility within week)
- **Intelligence**: anchor_confidence (high/low), history_count, history_years
- **Weekly Automation**: single reminder_date (Sunday 8 days before anchor week)
- **Tracking**: last_order_date_latest, source_occasion_ids, next_anchor_week_start

**Key Innovation**: All occasions are grouped into weekly windows (Monday-Sunday) with consolidated reminders sent on the Sunday that is exactly 8 days before the anchor week begins.

## Technical Implementation Plan

### Phase 1: Database Structure

#### 1.1 CRM Customers Table Migration
```php
// Migration: create_crm_customers_table.php
Schema::create('crm_customers', function (Blueprint $table) {
    $table->id();
    $table->string('customer_id')->unique(); // email-based ID
    $table->string('buyer_name');
    $table->string('primary_phone', 20)->nullable();
    $table->string('primary_email')->index();
    $table->date('first_order')->nullable();
    $table->date('last_order')->nullable();
    $table->integer('orders_count')->default(0);
    $table->text('fav_flavors')->nullable();
    $table->enum('eggs_ok', ['Yes', 'No', ''])->default('');
    $table->text('allergens')->nullable();
    $table->boolean('marketing_opt_in')->default(false);
    $table->string('channel_preference')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->index(['primary_email', 'customer_id']);
    $table->index('last_order');
});
```

#### 1.2 CRM Occasions Table Migration
```php
// Migration: create_crm_occasions_table.php
Schema::create('crm_occasions', function (Blueprint $table) {
    $table->id();
    $table->string('customer_id'); // FK to crm_customers
    $table->enum('occasion_type', ['birthday', 'anniversary', 'other']);
    $table->string('honoree_name')->nullable();
    $table->date('anchor_week_start_date'); // Always Monday of the anchor week
    $table->integer('anchor_window_days')->default(7); // Flexibility within the week
    $table->enum('anchor_confidence', ['high', 'low']);
    $table->date('last_order_date_latest')->nullable();
    $table->integer('history_count')->default(1);
    $table->string('history_years')->nullable();
    $table->string('source_occasion_ids')->nullable();
    $table->date('next_anchor_week_start')->nullable(); // Next Monday for this occasion
    $table->date('reminder_date')->nullable(); // Sunday 8 days before anchor_week_start_date
    $table->boolean('reminder_sent')->default(false);
    $table->timestamps();
    
    $table->foreign('customer_id')->references('customer_id')->on('crm_customers');
    $table->index(['customer_id', 'occasion_type']);
    $table->index(['anchor_week_start_date']);
    $table->index(['reminder_date', 'reminder_sent']);
    $table->index('next_anchor_week_start');
});

// Helper method to calculate reminder date
// For anchor_week_start_date = 2025-03-03 (Monday)
// reminder_date = 2025-02-23 (Sunday, 8 days before)
```

### Phase 2: Laravel Models and Relationships

#### 2.1 CrmCustomer Model
```php
// app/Models/CrmCustomer.php
class CrmCustomer extends Model
{
    protected $fillable = [
        'customer_id', 'buyer_name', 'primary_phone', 'primary_email',
        'first_order', 'last_order', 'orders_count', 'fav_flavors',
        'eggs_ok', 'allergens', 'marketing_opt_in', 'channel_preference', 'notes'
    ];
    
    protected $casts = [
        'first_order' => 'date',
        'last_order' => 'date',
        'marketing_opt_in' => 'boolean',
    ];
    
    public function occasions()
    {
        return $this->hasMany(CrmOccasion::class, 'customer_id', 'customer_id');
    }
    
    public function customOrders()
    {
        return $this->hasMany(CustomOrder::class, 'email', 'primary_email');
    }
}
```

#### 2.2 CrmOccasion Model
```php
// app/Models/CrmOccasion.php  
class CrmOccasion extends Model
{
    protected $fillable = [
        'customer_id', 'occasion_type', 'honoree_name', 'anchor_week_start_date',
        'anchor_window_days', 'anchor_confidence', 'last_order_date_latest',
        'history_count', 'history_years', 'source_occasion_ids',
        'next_anchor_week_start', 'reminder_date', 'reminder_sent'
    ];
    
    protected $casts = [
        'anchor_week_start_date' => 'date',
        'last_order_date_latest' => 'date',
        'next_anchor_week_start' => 'date',
        'reminder_date' => 'date',
        'reminder_sent' => 'boolean',
    ];
    
    public function customer()
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id', 'customer_id');
    }
    
    // Calculate reminder date (Sunday 8 days before anchor week Monday)
    public function calculateReminderDate()
    {
        return $this->anchor_week_start_date->subDays(8);
    }
    
    // Calculate next anticipated order date with recent order handling
    public function calculateNextAnticipatedOrderDate()
    {
        if (!$this->last_order_date_latest) return null;
        
        $lastOrderDate = Carbon::parse($this->last_order_date_latest);
        $today = now();
        
        // NEW LOGIC: If customer already ordered for this year (future date),
        // push next reminder to 12 months from that future date
        if ($lastOrderDate->gt($today)) {
            return $lastOrderDate->copy()->addYear();
        }
        
        // Existing logic for past orders...
        $targetMonth = $lastOrderDate->month;
        $targetDay = $lastOrderDate->day;
        $nextDate = Carbon::create($today->year, $targetMonth, $targetDay);
        
        if ($nextDate->lte($today)) {
            $nextDate = Carbon::create($today->year + 1, $targetMonth, $targetDay);
        }
        
        return $nextDate;
    }
    
    // Get all occasions for a specific week
    public static function getWeeklyOccasions($anchorWeekStart)
    {
        return self::where('anchor_week_start_date', $anchorWeekStart)
                   ->with('customer')
                   ->get();
    }
    
    // Scope for pending reminders
    public function scopePendingReminders($query)
    {
        return $query->where('reminder_sent', false)
                    ->where('reminder_date', '<=', now()->toDateString());
    }
}
```

### Phase 3: Admin Panel Integration

#### 3.1 Admin Routes Addition
```php
// routes/web.php - Admin CRM Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Existing admin routes...
    
    // CRM Routes
    Route::get('/crm/customers', [Admin\CrmController::class, 'customers'])->name('admin.crm.customers');
    Route::get('/crm/customers/{customer}', [Admin\CrmController::class, 'customerShow'])->name('admin.crm.customers.show');
    Route::get('/crm/occasions', [Admin\CrmController::class, 'occasions'])->name('admin.crm.occasions');
    Route::get('/crm/occasions/{occasion}', [Admin\CrmController::class, 'occasionShow'])->name('admin.crm.occasions.show');
    Route::get('/crm/dashboard', [Admin\CrmController::class, 'dashboard'])->name('admin.crm.dashboard');
});
```

#### 3.2 CRM Controller
```php
// app/Http/Controllers/Admin/CrmController.php
class CrmController extends Controller
{
    public function customers(Request $request)
    {
        $customers = CrmCustomer::with('occasions')
            ->when($request->search, function($query, $search) {
                $query->where('buyer_name', 'like', "%{$search}%")
                      ->orWhere('primary_email', 'like', "%{$search}%");
            })
            ->orderBy('last_order', 'desc')
            ->paginate(50);
            
        return view('admin.crm.customers.index', compact('customers'));
    }
    
    public function occasions(Request $request)
    {
        $occasions = CrmOccasion::with('customer')
            ->when($request->occasion_type, function($query, $type) {
                $query->where('occasion_type', $type);
            })
            ->when($request->upcoming, function($query) {
                $query->whereBetween('next_anchor_week_start', [now(), now()->addDays(60)]);
            })
            ->when($request->week_view, function($query) {
                // Group by anchor week for weekly view
                $query->orderBy('anchor_week_start_date', 'asc');
            }, function($query) {
                $query->orderBy('next_anchor_week_start', 'asc');
            })
            ->paginate(50);
            
        // Group occasions by week for better display
        $weeklyOccasions = $occasions->getCollection()->groupBy('anchor_week_start_date');
        
        return view('admin.crm.occasions.index', compact('occasions', 'weeklyOccasions'));
    }
    
    public function dashboard()
    {
        $stats = [
            'total_customers' => CrmCustomer::count(),
            'high_value_customers' => CrmCustomer::where('orders_count', '>=', 5)->count(),
            'upcoming_birthdays' => CrmOccasion::where('occasion_type', 'birthday')
                ->whereBetween('next_anchor_week_start', [now(), now()->addDays(30)])->count(),
            'upcoming_anniversaries' => CrmOccasion::where('occasion_type', 'anniversary')
                ->whereBetween('next_anchor_week_start', [now(), now()->addDays(30)])->count(),
            'pending_reminders' => CrmOccasion::pendingReminders()->count(),
            'this_week_occasions' => CrmOccasion::where('anchor_week_start_date', 
                now()->startOfWeek()->toDateString())->count(),
        ];
        
        return view('admin.crm.dashboard', compact('stats'));
    }
}
```

#### 3.3 Admin Navigation Update
Update `resources/views/admin/layout/sidebar.blade.php`:
```html
<!-- Add CRM Section to Admin Sidebar -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#crmCollapse">
        <i class="fas fa-users"></i>
        <span>CRM</span>
    </a>
    <div id="crmCollapse" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('admin.crm.dashboard') }}">Dashboard</a>
            <a class="collapse-item" href="{{ route('admin.crm.customers') }}">Customers</a>
            <a class="collapse-item" href="{{ route('admin.crm.occasions') }}">Occasions</a>
        </div>
    </div>
</li>
```

### Phase 4: Order Evaluation & Data Sync Workflow

#### 4.1 Order Processing Integration
Modify `OrderController@store` method to update CRM data:

```php
// In app/Http/Controllers/OrderController.php store() method
public function store(Request $request)
{
    // Existing order processing...
    
    // CRM Integration - Update/Create Customer
    $this->updateCrmCustomer($request);
    
    // Existing SMS/email logic...
}

private function updateCrmCustomer(Request $request)
{
    $customer = CrmCustomer::updateOrCreate(
        ['primary_email' => $request->email],
        [
            'customer_id' => $request->email,
            'buyer_name' => $request->name,
            'primary_phone' => $request->phone,
            'primary_email' => $request->email,
            'last_order' => now()->toDateString(),
        ]
    );
    
    // Update order count and first order date
    $customer->increment('orders_count');
    if (!$customer->first_order) {
        $customer->first_order = now()->toDateString();
        $customer->save();
    }
    
    // Extract flavor preferences from order
    if ($request->flavor_description) {
        $this->updateFlavorPreferences($customer, $request->flavor_description);
    }
    
    // Check for occasion indicators in the order
    $this->analyzeOrderForOccasions($customer, $request);
}
```

#### 4.2 Weekly Grouping Logic & Data Import

**Weekly Grouping Conversion Logic:**
```php
// Convert anchor_month/anchor_day to anchor_week_start_date
public static function calculateAnchorWeekStart($month, $day, $year = null) 
{
    $year = $year ?? now()->year;
    $anchorDate = Carbon::createFromDate($year, $month, $day);
    
    // Get the Monday of the week containing this anchor date
    return $anchorDate->startOfWeek(Carbon::MONDAY);
}

// Calculate reminder date (Sunday 8 days before)
public static function calculateReminderDate($anchorWeekStart) 
{
    return Carbon::parse($anchorWeekStart)->subDays(8); // Sunday 8 days before
}
```

#### 4.3 Data Import Commands
```php
// app/Console/Commands/ImportCrmData.php
class ImportCrmData extends Command
{
    protected $signature = 'crm:import {type} {file} {--convert-weekly : Convert daily occasions to weekly groups}';
    protected $description = 'Import CRM data from CSV files with weekly grouping support';
    
    public function handle()
    {
        $type = $this->argument('type');
        $file = $this->argument('file');
        $convertWeekly = $this->option('convert-weekly');
        
        if ($type === 'customers') {
            $this->importCustomers($file);
        } elseif ($type === 'occasions') {
            $this->importOccasions($file, $convertWeekly);
        }
    }
    
    private function importCustomers($file)
    {
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);
        
        $this->info('Importing customers...');
        $count = 0;
        
        foreach ($csv as $record) {
            CrmCustomer::updateOrCreate(
                ['customer_id' => $record['customer_id']],
                [
                    'buyer_name' => $record['buyer_name'],
                    'primary_phone' => $record['primary_phone'],
                    'primary_email' => $record['primary_email'],
                    'first_order' => $record['first_order'] ? Carbon::parse($record['first_order']) : null,
                    'last_order' => $record['last_order'] ? Carbon::parse($record['last_order']) : null,
                    'orders_count' => (int)$record['orders_count'],
                    'fav_flavors' => $record['fav_flavors'],
                    'eggs_ok' => $record['eggs_ok'],
                    'allergens' => $record['allergens'],
                    'marketing_opt_in' => !empty($record['marketing_opt_in']),
                    'channel_preference' => $record['channel_preference'],
                    'notes' => $record['notes'],
                ]
            );
            $count++;
        }
        
        $this->info("Imported {$count} customers successfully.");
    }
    
    private function importOccasions($file, $convertWeekly = true)
    {
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);
        
        $this->info('Importing occasions with weekly grouping...');
        $count = 0;
        $weeklyGroups = [];
        
        foreach ($csv as $record) {
            if ($convertWeekly) {
                // Convert anchor_month/anchor_day to weekly grouping
                $anchorWeekStart = $this->calculateAnchorWeekStart(
                    (int)$record['anchor_month'], 
                    (int)$record['anchor_day']
                );
                $reminderDate = $this->calculateReminderDate($anchorWeekStart);
                $nextAnchorWeekStart = $anchorWeekStart->copy()->addYear();
            } else {
                // Use existing data if already in weekly format
                $anchorWeekStart = Carbon::parse($record['anchor_week_start_date']);
                $reminderDate = Carbon::parse($record['reminder_date']);
                $nextAnchorWeekStart = Carbon::parse($record['next_anchor_week_start']);
            }
            
            CrmOccasion::updateOrCreate(
                [
                    'customer_id' => $record['customer_id'],
                    'occasion_type' => $record['occasion_type'],
                    'anchor_week_start_date' => $anchorWeekStart->toDateString(),
                ],
                [
                    'honoree_name' => $record['honoree_name'] ?? null,
                    'anchor_window_days' => (int)($record['anchor_window_days'] ?? 7),
                    'anchor_confidence' => $record['anchor_confidence'],
                    'last_order_date_latest' => $record['last_order_date_latest'] ? 
                        Carbon::parse($record['last_order_date_latest']) : null,
                    'history_count' => (int)$record['history_count'],
                    'history_years' => $record['history_years'],
                    'source_occasion_ids' => $record['source_occasion_ids'],
                    'next_anchor_week_start' => $nextAnchorWeekStart,
                    'reminder_date' => $reminderDate,
                    'reminder_sent' => false,
                ]
            );
            $count++;
            
            // Track weekly groupings for summary
            $weekKey = $anchorWeekStart->toDateString();
            $weeklyGroups[$weekKey] = ($weeklyGroups[$weekKey] ?? 0) + 1;
        }
        
        $this->info("Imported {$count} occasions into " . count($weeklyGroups) . " weekly groups.");
        $this->table(['Week Start (Monday)', 'Occasions Count'], 
            collect($weeklyGroups)->map(fn($count, $week) => [$week, $count])->toArray());
    }
    
    private function calculateAnchorWeekStart($month, $day, $year = null)
    {
        $year = $year ?? now()->year;
        $anchorDate = Carbon::createFromDate($year, $month, $day);
        return $anchorDate->startOfWeek(Carbon::MONDAY);
    }
    
    private function calculateReminderDate($anchorWeekStart)
    {
        return $anchorWeekStart->copy()->subDays(8); // Sunday 8 days before
    }
}

// Usage:
// php artisan crm:import customers /path/to/customers_master.csv
// php artisan crm:import occasions /path/to/occasions_master.csv --convert-weekly
```

### Phase 5: Advanced CRM Features

#### 5.1 Weekly Reminder System
```php
// app/Console/Commands/SendCrmReminders.php
class SendCrmReminders extends Command
{
    protected $signature = 'crm:reminders {--dry-run : Show what would be sent without sending}';
    
    public function handle()
    {
        $today = now()->toDateString();
        $isDryRun = $this->option('dry-run');
        
        // Get all occasions with reminders due today that haven't been sent
        $pendingReminders = CrmOccasion::pendingReminders()
            ->where('reminder_date', $today)
            ->where('anchor_confidence', 'high')
            ->get()
            ->groupBy('anchor_week_start_date'); // Group by week
            
        foreach ($pendingReminders as $anchorWeekStart => $weekOccasions) {
            $this->sendWeeklyReminderBatch($weekOccasions, $anchorWeekStart, $isDryRun);
        }
        
        $this->info("Processed " . $pendingReminders->count() . " weekly reminder batches.");
    }
    
    private function sendWeeklyReminderBatch($occasions, $anchorWeekStart, $isDryRun = false)
    {
        $weekStart = Carbon::parse($anchorWeekStart);
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        $this->info("Processing week: {$weekStart->format('M d')} - {$weekEnd->format('M d, Y')}");
        
        // Group occasions by customer to avoid duplicate notifications
        $customerOccasions = $occasions->groupBy('customer_id');
        
        foreach ($customerOccasions as $customerId => $customerEvents) {
            $customer = $customerEvents->first()->customer;
            
            if ($isDryRun) {
                $this->line("  Would notify: {$customer->buyer_name} ({$customer->primary_email})");
                $this->line("    Events: " . $customerEvents->pluck('occasion_type')->implode(', '));
            } else {
                // Send consolidated weekly reminder
                $this->sendConsolidatedReminder($customer, $customerEvents, $weekStart);
                
                // Mark all occasions as reminded
                CrmOccasion::whereIn('id', $customerEvents->pluck('id'))
                    ->update(['reminder_sent' => true]);
            }
        }
    }
    
    private function sendConsolidatedReminder($customer, $occasions, $weekStart)
    {
        // Send email/SMS with all occasions for this customer in the upcoming week
        // This replaces the individual 30d, 14d, 7d reminder system
        
        $weekEnd = $weekStart->copy()->endOfWeek();
        $occasionsList = $occasions->map(function($occasion) {
            return $occasion->occasion_type . ($occasion->honoree_name ? " for {$occasion->honoree_name}" : '');
        })->implode(', ');
        
        // Implementation for email/SMS sending would go here
        Log::info("Weekly reminder sent to {$customer->primary_email} for week {$weekStart->format('M d')}-{$weekEnd->format('d')}: {$occasionsList}");
    }
}

// Schedule in app/Console/Kernel.php:
// $schedule->command('crm:reminders')->dailyAt('09:00'); // Run every Sunday at 9 AM
```

#### 5.2 Customer Segmentation
```php
// Add methods to CrmCustomer model
public function scopeHighValue($query)
{
    return $query->where('orders_count', '>=', 5);
}

public function scopeRecentlyActive($query)
{
    return $query->where('last_order', '>=', now()->subMonths(6));
}

public function scopeHasAllergies($query)
{
    return $query->whereNotNull('allergens')->where('allergens', '!=', '');
}
```

## Weekly Grouping Benefits

### Why Weekly Grouping is Superior:

1. **Simplified Reminder Management**: Instead of tracking 30d, 14d, 7d reminders for each occasion, we have one consolidated reminder per week
2. **Batch Processing Efficiency**: All occasions in a week (Monday-Sunday) are processed together
3. **Reduced Notification Fatigue**: Customers receive one weekly summary instead of multiple individual reminders
4. **Consistent Timing**: All reminders go out on Sunday, 8 days before the occasion week begins
5. **Better Analytics**: Weekly grouping provides clearer insights into seasonal patterns
6. **Scalability**: Much more efficient for processing large volumes of occasions

### Example Weekly Flow:
```
Occasion Week: March 3-9, 2025 (Monday-Sunday)
├── Birthday: Sarah (March 4)
├── Anniversary: John & Mary (March 7)
└── Birthday: Mike (March 8)

Reminder Date: February 23, 2025 (Sunday, 8 days before March 3)
└── Send ONE consolidated reminder covering all three occasions
```

## Implementation Timeline

### Week 1: Foundation
- [ ] Create database migrations with weekly grouping structure
- [ ] Build CrmCustomer and CrmOccasion models with weekly logic
- [ ] Create data conversion logic for existing CSV data
- [ ] Import and convert existing CSV data to weekly format

### Week 2: Admin Integration
- [ ] Build CRM controller with weekly grouping views
- [ ] Update admin navigation for CRM section
- [ ] Create customer listing and weekly occasion views
- [ ] Build weekly dashboard with consolidated metrics

### Week 3: Order Integration & Testing
- [ ] Implement order evaluation workflow for weekly updates
- [ ] Add customer data sync to order processing
- [ ] Test order-to-CRM data flow with weekly grouping
- [ ] Validate weekly reminder calculations

### Week 4: Advanced Features & Deployment
- [ ] Implement weekly reminder system with batch processing
- [ ] Add customer segmentation and analytics
- [ ] Create automated weekly reminder scheduling
- [ ] Deploy and monitor initial weekly reminders

## Data Privacy & Security Considerations

1. **GDPR Compliance**: Add data retention policies and customer consent tracking
2. **Data Encryption**: Sensitive customer data should be encrypted at rest
3. **Access Control**: Limit CRM access to authorized admin users only
4. **Audit Trail**: Log all CRM data modifications for compliance

## Success Metrics

1. **Customer Retention**: Track repeat orders through CRM data
2. **Occasion Accuracy**: Monitor prediction accuracy for birthday/anniversary orders
3. **Marketing Effectiveness**: Measure conversion rates from reminder campaigns
4. **Order Value Growth**: Track average order value increases through personalization

## Next Steps

1. Review and approve this implementation plan
2. Begin Phase 1 database migration development
3. Create initial data import from existing CSV files
4. Iteratively build and test each phase before moving to the next

---

*This CRM system will transform customer relationship management for Chocolate Therapy by providing data-driven insights, automated reminders, and personalized customer experiences.*

Flexible Seeding: The seeders work correctly whether you're:
    - Fresh seeding in production (php artisan crm:seed)
    - Importing new CSV data (php artisan crm:import)
    - Updating existing occasions (php artisan crm:update-dates)