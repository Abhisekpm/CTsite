<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Added Carbon for date comparison
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail; // Import Mail facade
use App\Mail\CustomerOrderPricingMail; // Import pricing Mailable
use App\Jobs\SendPricingNotificationSMS; // Import pricing SMS job
use App\Jobs\SendPickupReminderSMS; // Import pickup reminder SMS job
use Exception; // Import base Exception
use Illuminate\Validation\Rule; // Import Rule for validation

class OrderController extends Controller
{
    /**
     * Display a listing of the custom orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get status filter from request, default to null (all)
        $status = $request->query('status');
        $validStatuses = ['pending', 'priced', 'confirmed']; // Add others if needed
        $filter = $request->query('filter'); // Get the new filter parameter

        // Start query builder
        $query = CustomOrder::orderBy('pickup_date', 'asc');

        // Apply status filter if a valid status is provided
        if ($status && in_array($status, $validStatuses)) {
            $query->where('status', $status);
        }

        // Default to future orders unless 'filter=all_time' is explicitly passed.
        if ($filter !== 'all_time') { // If filter is 'future', null, or anything else, apply future condition.
            $query->whereDate('pickup_date', '>=', Carbon::today());
        }

        // Fetch orders with pagination
        // Important: withQueryString() will now preserve both 'status' and 'filter' parameters
        $orders = $query->paginate(20)->withQueryString(); 

        // Pass orders and current filters to the view
        return view('admin.orders.index', [
            'orders' => $orders,
            'currentStatus' => $status, // Pass the current status filter value
            // No need to pass the future filter state explicitly, 
            // the view logic already checks request()->query('filter')
        ]);
    }

    /**
     * Display the specified custom order details and pricing form.
     *
     * @param  \App\Models\CustomOrder  $order
     * @return \Illuminate\View\View
     */
    public function show(CustomOrder $order)
    {
        // The $order is automatically resolved by route model binding

        // Placeholder view name - create this view next
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified order's price and potentially status.
     * Trigger SMS notification to customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomOrder  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePrice(Request $request, CustomOrder $order)
    {
        // Validate the incoming request (price is required and numeric)
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            // Add validation for status change if you implement that here
        ]);

        $smsSent = false;
        $smsError = null;

        // Update the order
        try {
            $originalStatus = $order->status;
            $order->price = $validated['price'];
            // Only update status to 'priced' if it's not already confirmed or cancelled etc.
            if ($originalStatus == 'pending') {
                 $order->status = 'priced';
            }
            $order->save();

            // --- Queue Customer Notifications with Price (only if status became 'priced' or was already 'priced') ---
            if ($order->status == 'priced') {
                // Queue pricing email notification
                Mail::to($order->email)->queue(new CustomerOrderPricingMail($order));
                logger()->info("Pricing email queued for order #{$order->id}.");
                
                // Queue pricing SMS notification
                SendPricingNotificationSMS::dispatch($order);
                logger()->info("Pricing SMS queued for order #{$order->id}.");
                
                // Set success flags for UI feedback
                $emailSent = true;
                $smsSent = true;
                $emailError = null;
                $smsError = null;
            }
            // --- End SMS Sending Logic ---

            $notificationStatus = [];
            if ($emailSent) {
                $notificationStatus[] = 'Email sent';
            } elseif ($emailError) {
                $notificationStatus[] = 'Email failed: ' . $emailError;
            }
            
            if ($smsSent) {
                $notificationStatus[] = 'SMS sent';
            } elseif ($smsError) {
                $notificationStatus[] = 'SMS failed: ' . $smsError;
            }
            
            $successMessage = 'Order price updated.';
            if (!empty($notificationStatus)) {
                $successMessage .= ' ' . implode(', ', $notificationStatus) . '.';
            }
            
            return redirect()->route('admin.orders.show', $order)->with('success', $successMessage);

        } catch (Exception $e) { // Catch errors during order update itself
            logger()->error("Error updating price for order #{$order->id}: " . $e->getMessage());
            return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to update order price. Please try again.')->withInput();
        }
    }

    /**
     * Update the specified order's details (customer name, cake details).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomOrder  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CustomOrder $order)
    {
        // Validate the incoming request for the editable fields
        // Removed 'required' for fields assumed to exist from initial order
        $validated = $request->validate([
            'customer_name' => 'sometimes|string|max:255',
            'cake_size' => 'sometimes|string|max:100',
            'cake_flavor' => 'sometimes|string|max:100',
            'cake_sponge' => 'nullable|string|max:100',
            'eggs_ok' => ['sometimes', Rule::in(['Yes', 'No'])],
            'message_on_cake' => 'nullable|string|max:500',
            'allergies' => 'nullable|string|max:500',
            'custom_decoration' => 'nullable|string|max:1000',
            'pickup_date' => 'sometimes|date',
            'pickup_time' => 'sometimes|date_format:H:i', // Corrected to H:i for 24-hour format
        ]);

        try {
            // Update the order with validated data
            $order->update($validated);

            return redirect()->route('admin.orders.show', $order)->with('success', 'Order details updated successfully.');

        } catch (Exception $e) {
            logger()->error("Error updating details for order #{$order->id}: " . $e->getMessage());
            return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to update order details. Please try again.')->withInput();
        }
    }

    /**
     * Manually confirm an order.
     *
     * @param  \App\Models\CustomOrder  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(CustomOrder $order)
    {
        if ($order->status !== 'priced') {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Order cannot be confirmed unless status is priced.');
        }

        try {
            $order->status = 'confirmed';
            $order->save();
            // Optionally: Trigger any admin/customer notifications for manual confirmation?
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order manually marked as confirmed.');
        } catch (Exception $e) {
            logger()->error("Error manually confirming order #{$order->id}: " . $e->getMessage());
            return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to confirm order.');
        }
    }

    /**
     * Manually cancel an order.
     *
     * @param  \App\Models\CustomOrder  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(CustomOrder $order)
    {
        if (in_array($order->status, ['confirmed', 'cancelled'])) {
             return redirect()->route('admin.orders.show', $order)->with('error', 'Order is already confirmed or cancelled.');
        }

        try {
            $order->status = 'cancelled';
            $order->save();
             // Optionally: Trigger any admin/customer notifications for manual cancellation?
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order manually marked as cancelled.');
        } catch (Exception $e) {
            logger()->error("Error manually cancelling order #{$order->id}: " . $e->getMessage());
            return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to cancel order.');
        }
    }

    /**
     * Generate a printable view of today's dispatch orders.
     *
     * @return \Illuminate\View\View
     */
    public function printTodaysDispatch()
    {
        $today = Carbon::today();
        $todaysConfirmedOrders = CustomOrder::whereDate('pickup_date', $today)
                                          ->where('status', 'confirmed') // Added filter for confirmed status
                                          ->orderBy('pickup_time', 'asc')
                                          ->get();
        
        return view('admin.orders.print_dispatch', [
            'orders' => $todaysConfirmedOrders,
            'printDate' => $today->format('l, F j, Y') 
        ]);
    }

    /**
     * Generate a printable view of dispatch orders for a specific date.
     *
     * @param  string $dateString The date in Y-m-d format.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function printDispatchForDate($dateString)
    {
        try {
            $selectedDate = Carbon::parse($dateString);
        } catch (\Exception $e) {
            // Handle invalid date format
            return redirect()->route('admin.orders.index')->with('error', 'Invalid date format for dispatch.');
        }

        $confirmedOrdersForDate = CustomOrder::whereDate('pickup_date', $selectedDate)
                                          ->where('status', 'confirmed')
                                          ->orderBy('pickup_time', 'asc')
                                          ->get();
        
        return view('admin.orders.print_dispatch', [
            'orders' => $confirmedOrdersForDate,
            'printDate' => $selectedDate->format('l, F j, Y') 
        ]);
    }

    /**
     * Send a pickup reminder SMS to the customer.
     *
     * @param  \App\Models\CustomOrder  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPickupReminder(CustomOrder $order)
    {
        if (!in_array($order->status, ['confirmed', 'priced'])) {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Pickup reminder can only be sent for priced or confirmed orders.');
        }

        try {
            // Queue pickup reminder SMS
            SendPickupReminderSMS::dispatch($order);
            logger()->info("Pickup reminder SMS queued for order #{$order->id}.");
            
            return redirect()->route('admin.orders.show', $order)->with('success', 'Pickup reminder SMS has been queued and will be sent shortly.');
            
        } catch (Exception $e) {
            logger()->error('Error queuing pickup reminder for order #' . $order->id . ': ' . $e->getMessage());
            return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to queue pickup reminder. Please try again.');
        }
    }
}
