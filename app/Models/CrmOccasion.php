<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CrmOccasion extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'occasion_type', 'honoree_name', 'anchor_week_start_date',
        'anchor_window_days', 'anchor_confidence', 'last_order_date_latest',
        'history_count', 'history_years', 'source_occasion_ids',
        'next_anchor_week_start', 'reminder_date', 'reminder_sent',
        'next_anticipated_order_date'
    ];

    protected $casts = [
        'anchor_week_start_date' => 'date',
        'last_order_date_latest' => 'date',
        'next_anchor_week_start' => 'date',
        'next_anticipated_order_date' => 'date',
        'reminder_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id', 'customer_id');
    }

    public function calculateReminderDate()
    {
        return $this->anchor_week_start_date->copy()->subDays(8);
    }

    public static function getWeeklyOccasions($anchorWeekStart)
    {
        return self::where('anchor_week_start_date', $anchorWeekStart)
                   ->with('customer')
                   ->get();
    }

    public function scopePendingReminders($query)
    {
        return $query->where('reminder_sent', false)
                    ->where('reminder_date', '<=', now()->toDateString());
    }

    public static function calculateAnchorWeekStart($month, $day, $year = null)
    {
        $year = $year ?? now()->year;
        $anchorDate = Carbon::createFromDate($year, $month, $day);
        return $anchorDate->startOfWeek(Carbon::MONDAY);
    }

    public static function calculateReminderDateStatic($anchorWeekStart)
    {
        return Carbon::parse($anchorWeekStart)->subDays(8);
    }

    /**
     * Calculate the next anticipated order date based on last order date
     * This takes the month and day from last_order_date_latest and finds the next occurrence
     * within the next 12 months from today. If the customer has already ordered recently
     * (last order is in the future), we wait 12 months from that date.
     */
    public function calculateNextAnticipatedOrderDate()
    {
        if (!$this->last_order_date_latest) {
            return null;
        }

        $lastOrderDate = Carbon::parse($this->last_order_date_latest);
        $today = now();
        
        // If the last order date is in the future (customer already ordered for this year's occasion),
        // set next anticipated order to 12 months from that future date
        if ($lastOrderDate->gt($today)) {
            return $lastOrderDate->copy()->addYear();
        }
        
        // Get month and day from the last order
        $targetMonth = $lastOrderDate->month;
        $targetDay = $lastOrderDate->day;
        
        // Try current year first
        $nextDate = Carbon::create($today->year, $targetMonth, $targetDay);
        
        // If the date has already passed this year, use next year
        if ($nextDate->lte($today)) {
            $nextDate = Carbon::create($today->year + 1, $targetMonth, $targetDay);
        }
        
        // Make sure it's within the next 12 months
        if ($nextDate->gt($today->copy()->addMonths(12))) {
            $nextDate = Carbon::create($today->year, $targetMonth, $targetDay);
        }
        
        return $nextDate;
    }

    /**
     * Calculate and update all related dates based on next_anticipated_order_date
     */
    public function updateCalculatedDates()
    {
        if (!$this->next_anticipated_order_date) {
            $this->next_anticipated_order_date = $this->calculateNextAnticipatedOrderDate();
        }

        if ($this->next_anticipated_order_date) {
            // Calculate anchor week start (Monday of the week containing the anticipated date)
            $this->anchor_week_start_date = $this->next_anticipated_order_date->copy()->startOfWeek(Carbon::MONDAY);
            
            // Set next_anchor_week_start to the same value for consistency
            $this->next_anchor_week_start = $this->anchor_week_start_date;
            
            // Calculate reminder date (Sunday 8 days before the anchor week)
            $this->reminder_date = $this->anchor_week_start_date->copy()->subDays(8);
            
            // Reset reminder sent status
            $this->reminder_sent = false;
        }
    }

    /**
     * Update occasion when a new order is placed by the customer
     * This should be called whenever a customer places a new order
     */
    public function updateWithNewOrder($orderDate = null)
    {
        $orderDate = $orderDate ? Carbon::parse($orderDate) : now();
        
        // Update the last order date if this is more recent
        if (!$this->last_order_date_latest || 
            Carbon::parse($this->last_order_date_latest)->lt($orderDate)) {
            $this->last_order_date_latest = $orderDate;
            $this->history_count = $this->history_count + 1;
        }
        
        // Recalculate all dates based on the new order information
        $this->next_anticipated_order_date = null; // Force recalculation
        $this->updateCalculatedDates();
        
        return $this;
    }

    /**
     * Static method to calculate next anticipated order date from a given date
     * Includes logic to handle recent orders (future dates)
     */
    public static function calculateNextAnticipatedOrderDateStatic($lastOrderDate)
    {
        if (!$lastOrderDate) {
            return null;
        }

        $lastOrderDate = Carbon::parse($lastOrderDate);
        $today = now();
        
        // If the last order date is in the future (customer already ordered for this year's occasion),
        // set next anticipated order to 12 months from that future date
        if ($lastOrderDate->gt($today)) {
            return $lastOrderDate->copy()->addYear();
        }
        
        // Get month and day from the last order
        $targetMonth = $lastOrderDate->month;
        $targetDay = $lastOrderDate->day;
        
        // Try current year first
        $nextDate = Carbon::create($today->year, $targetMonth, $targetDay);
        
        // If the date has already passed this year, use next year
        if ($nextDate->lte($today)) {
            $nextDate = Carbon::create($today->year + 1, $targetMonth, $targetDay);
        }
        
        // Make sure it's within the next 12 months
        if ($nextDate->gt($today->copy()->addMonths(12))) {
            $nextDate = Carbon::create($today->year, $targetMonth, $targetDay);
        }
        
        return $nextDate;
    }
}
