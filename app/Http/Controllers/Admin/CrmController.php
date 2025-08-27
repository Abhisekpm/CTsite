<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CrmCustomer;
use App\Models\CrmOccasion;
use Carbon\Carbon;

class CrmController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_customers' => CrmCustomer::count(),
            'high_value_customers' => CrmCustomer::where('orders_count', '>=', 5)->count(),
            'upcoming_birthdays' => CrmOccasion::where('occasion_type', 'birthday')
                ->whereBetween('next_anticipated_order_date', [now(), now()->addDays(30)])->count(),
            'upcoming_anniversaries' => CrmOccasion::where('occasion_type', 'anniversary')
                ->whereBetween('next_anticipated_order_date', [now(), now()->addDays(30)])->count(),
            'pending_reminders' => CrmOccasion::where('reminder_sent', false)
                ->where('reminder_date', '<=', now()->toDateString())->count(),
            'this_week_occasions' => CrmOccasion::whereBetween('next_anticipated_order_date', 
                [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Weekly occasions summary for next 8 weeks
        $upcomingWeeks = [];
        for ($i = 0; $i < 8; $i++) {
            $weekStart = now()->addWeeks($i)->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->endOfWeek();
            $count = CrmOccasion::whereBetween('next_anticipated_order_date', [$weekStart, $weekEnd])->count();
            
            if ($count > 0) {
                $upcomingWeeks[] = [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'occasion_count' => $count,
                    'is_current_week' => $i === 0,
                ];
            }
        }

        // Recent customer activity
        $recentCustomers = CrmCustomer::orderBy('last_order', 'desc')
            ->whereNotNull('last_order')
            ->take(10)
            ->get();

        return view('admin.crm.dashboard', compact('stats', 'upcomingWeeks', 'recentCustomers'));
    }

    public function customers(Request $request)
    {
        $query = CrmCustomer::with('occasions');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('buyer_name', 'like', "%{$search}%")
                  ->orWhere('primary_email', 'like', "%{$search}%")
                  ->orWhere('primary_phone', 'like', "%{$search}%");
            });
        }

        // Filter by customer segment
        if ($request->filled('segment')) {
            switch ($request->segment) {
                case 'high_value':
                    $query->where('orders_count', '>=', 5);
                    break;
                case 'recent':
                    $query->where('last_order', '>=', now()->subMonths(6));
                    break;
                case 'inactive':
                    $query->where('last_order', '<', now()->subMonths(6))
                          ->whereNotNull('last_order');
                    break;
                case 'allergies':
                    $query->whereNotNull('allergens')->where('allergens', '!=', '');
                    break;
            }
        }

        // Sort options
        $sortBy = $request->get('sort', 'last_order');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['buyer_name', 'primary_email', 'orders_count', 'last_order'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $customers = $query->paginate(25)->withQueryString();

        return view('admin.crm.customers.index', compact('customers'));
    }

    public function customerShow(CrmCustomer $customer)
    {
        $customer->load('occasions', 'customOrders');
        
        // Calculate customer insights
        $insights = [
            'days_since_last_order' => $customer->last_order ? 
                now()->diffInDays(Carbon::parse($customer->last_order)) : null,
            'average_order_frequency' => $this->calculateOrderFrequency($customer),
            'upcoming_occasions' => $customer->occasions()
                ->whereBetween('next_anticipated_order_date', [now(), now()->addMonths(3)])
                ->orderBy('next_anticipated_order_date')
                ->get(),
        ];

        return view('admin.crm.customers.show', compact('customer', 'insights'));
    }

    public function occasions(Request $request)
    {
        $query = CrmOccasion::with('customer');

        // Filter by occasion type
        if ($request->filled('occasion_type')) {
            $query->where('occasion_type', $request->occasion_type);
        }

        // Filter by order history
        if ($request->filled('order_filter')) {
            switch ($request->order_filter) {
                case 'recent_orders':
                    $query->whereNotNull('last_order_date_latest');
                    break;
                case 'no_orders':
                    $query->whereNull('last_order_date_latest');
                    break;
            }
        }

        // Filter by time range - use next_anticipated_order_date for future dates
        $timeframe = $request->get('timeframe', 'upcoming');
        switch ($timeframe) {
            case 'upcoming':
                $query->whereBetween('next_anticipated_order_date', [now(), now()->addMonths(6)]);
                break;
            case 'current_month':
                $query->whereBetween('next_anticipated_order_date', [
                    now()->startOfMonth(), 
                    now()->endOfMonth()
                ]);
                break;
            case 'next_month':
                $query->whereBetween('next_anticipated_order_date', [
                    now()->addMonth()->startOfMonth(), 
                    now()->addMonth()->endOfMonth()
                ]);
                break;
            case 'next_3_months':
                $query->whereBetween('next_anticipated_order_date', [now(), now()->addMonths(3)]);
                break;
            case 'all':
                $query->whereNotNull('next_anticipated_order_date');
                break;
        }

        // Check if weekly view is requested
        if (request('view') === 'weekly') {
            return $this->weeklyOccasionsView($query);
        }

        // Apply sorting
        $sort = $request->get('sort', 'reminder_date');
        switch ($sort) {
            case 'reminder_date':
                $query->orderBy('reminder_date', 'asc');
                break;
            case 'anchor_week':
                $query->orderBy('next_anchor_week_start', 'asc');
                break;
            case 'last_order_date':
                $query->orderBy('last_order_date_latest', 'desc');
                break;
            case 'customer_name':
                $query->join('crm_customers', 'crm_occasions.customer_id', '=', 'crm_customers.customer_id')
                     ->orderBy('crm_customers.buyer_name', 'asc')
                     ->select('crm_occasions.*');
                break;
            case 'occasion_type':
                $query->orderBy('occasion_type', 'asc');
                break;
            default:
                $query->orderBy('next_anchor_week_start', 'asc');
                break;
        }

        // Standard list view
        $occasions = $query->paginate(25)
            ->withQueryString();

        return view('admin.crm.occasions.index', compact('occasions'));
    }

    public function occasionShow(CrmOccasion $occasion)
    {
        $occasion->load('customer');
        
        // Get other occasions for the same week
        $weekOccasions = CrmOccasion::where('anchor_week_start_date', $occasion->anchor_week_start_date)
            ->where('id', '!=', $occasion->id)
            ->with('customer')
            ->get();

        return view('admin.crm.occasions.show', compact('occasion', 'weekOccasions'));
    }

    private function weeklyOccasionsView($query)
    {
        $showEmpty = request('show_empty', '0') == '1';
        
        // Get occasions
        $occasions = $query->with('customer')->orderBy('next_anticipated_order_date', 'asc')->get();
        
        // Group occasions by week
        $weeklyData = [];
        
        foreach ($occasions as $occasion) {
            if (!$occasion->next_anticipated_order_date || !$occasion->anchor_week_start_date) {
                continue;
            }
            
            $weekStart = $occasion->anchor_week_start_date->copy();
            $weekKey = $weekStart->format('Y-m-d');
            
            // Initialize week data if not exists
            if (!isset($weeklyData[$weekKey])) {
                $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
                
                $weeklyData[$weekKey] = [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'occasions' => collect(),
                    'total_occasions' => 0,
                    'high_confidence_count' => 0,
                    'is_current_week' => $weekStart->isCurrentWeek(),
                    'is_reminder_week' => false,
                    'occasion_types' => []
                ];
            }
            
            // Add occasion to week
            $weeklyData[$weekKey]['occasions']->push($occasion);
            $weeklyData[$weekKey]['total_occasions']++;
            
            if ($occasion->anchor_confidence === 'high') {
                $weeklyData[$weekKey]['high_confidence_count']++;
            }
            
            $occasionType = $occasion->occasion_type ?? 'unknown';
            if (!isset($weeklyData[$weekKey]['occasion_types'][$occasionType])) {
                $weeklyData[$weekKey]['occasion_types'][$occasionType] = 0;
            }
            $weeklyData[$weekKey]['occasion_types'][$occasionType]++;
            
            // Check if this week has reminders due
            if ($occasion->reminder_date && $occasion->reminder_date->between($weekStart, $weekStart->copy()->addDays(6))) {
                $weeklyData[$weekKey]['is_reminder_week'] = true;
            }
        }
        
        // Convert to indexed array and sort by week start date
        $weeklyData = array_values($weeklyData);
        usort($weeklyData, function($a, $b) {
            return $a['week_start']->lt($b['week_start']) ? -1 : ($a['week_start']->gt($b['week_start']) ? 1 : 0);
        });
        
        return view('admin.crm.occasions.weekly', compact('weeklyData'));
    }

    private function calculateOrderFrequency(CrmCustomer $customer)
    {
        if (!$customer->first_order || !$customer->last_order || $customer->orders_count <= 1) {
            return null;
        }

        $daysBetween = Carbon::parse($customer->first_order)->diffInDays(Carbon::parse($customer->last_order));
        return $daysBetween > 0 ? round($daysBetween / $customer->orders_count, 1) : null;
    }
}
