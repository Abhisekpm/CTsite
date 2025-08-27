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
}
