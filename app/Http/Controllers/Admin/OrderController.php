<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Added Carbon for date comparison
use Illuminate\Support\Facades\Http;
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

            // --- Trigger Customer SMS with Price (only if status became 'priced' or was already 'priced') ---
            if ($order->status == 'priced') {
                try {
                    $simpleTextingApiKey = env('SIMPLETEXTING_API_KEY');
                    $customerPhone = $order->phone; // Ensure E.164 format if possible

                    if ($simpleTextingApiKey && $customerPhone) {
                        $formattedPrice = number_format($order->price, 2);
                        
                        $messageBody = "Your custom cake order #{$order->id} is priced at $${formattedPrice}. Please pay a deposit of $20 via Zelle (5179806354) or Venmo (@Nupur-Kundalia) and reply YES here to confirm. If there are questions contact the bakery at (267)-541-8620";

                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $simpleTextingApiKey,
                            'Content-Type' => 'application/json'
                        ])->post('https://api-app2.simpletexting.com/v2/api/messages', [
                            'contactPhone' => $customerPhone,
                            'mode' => 'AUTO',
                            'text' => $messageBody
                        ]);

                        if ($response->successful()) {
                            $smsSent = true;
                            logger()->info("Pricing SMS sent for order #{$order->id}.");
                        } else {
                            $smsError = 'SimpleTexting Error: ' . $response->body();
                            logger()->error('SimpleTexting error sending pricing SMS for order #' . $order->id . ': ' . $response->body());
                        }

                    } else {
                         $smsError = 'SimpleTexting SMS not sent. Missing API key in .env';
                         logger()->warning('SimpleTexting SMS not sent for order #' . $order->id . '. Missing API key.');
                    }
                } catch (Exception $e) { // Catch general exceptions during SMS sending
                    $smsError = 'SMS Error: ' . $e->getMessage();
                    logger()->error('General Exception sending pricing SMS for order #' . $order->id . ': ' . $e->getMessage());
                }
            }
            // --- End SMS Sending Logic ---

            $successMessage = 'Order price updated.' . ($smsSent ? ' SMS notification sent.' : ($smsError ? ' SMS failed: ' . $smsError : ' No SMS sent (status not \'priced\').'));
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

        $smsSent = false;
        $smsError = null;

        try {
            $simpleTextingApiKey = env('SIMPLETEXTING_API_KEY');
            $customerPhone = $order->phone;

            if ($simpleTextingApiKey && $customerPhone) {
                $pickupDate = Carbon::parse($order->pickup_date)->format('l, F jS');
                $pickupTime = Carbon::parse($order->pickup_time)->format('h:i A');
                
                $messageBody = "Reminder: Your cake order #{$order->id} from Chocolate Therapy is scheduled for pickup on {$pickupDate} at {$pickupTime}. If you have any questions, please contact us at (267)541-8620.";

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $simpleTextingApiKey,
                    'Content-Type' => 'application/json'
                ])->post('https://api-app2.simpletexting.com/v2/api/messages', [
                    'contactPhone' => $customerPhone,
                    'mode' => 'AUTO',
                    'text' => $messageBody
                ]);

                if ($response->successful()) {
                    $smsSent = true;
                    logger()->info("Pickup reminder SMS sent for order #{$order->id}.");
                    return redirect()->route('admin.orders.show', $order)->with('success', 'Pickup reminder SMS sent successfully.');
                } else {
                    $smsError = 'SimpleTexting Error: ' . $response->body();
                    logger()->error('SimpleTexting error sending pickup reminder for order #' . $order->id . ': ' . $response->body());
                    return redirect()->route('admin.orders.show', $order)->with('error', 'Failed to send reminder: ' . $smsError);
                }
            } else {
                $smsError = 'SimpleTexting SMS not sent. Missing API key in .env or customer phone.';
                logger()->warning('SimpleTexting SMS not sent for order #' . $order->id . '. Missing API key or customer phone for reminder.');
                return redirect()->route('admin.orders.show', $order)->with('error', $smsError);
            }
        } catch (Exception $e) {
            logger()->error('General Exception sending pickup reminder for order #' . $order->id . ': ' . $e->getMessage());
            return redirect()->route('admin.orders.show', $order)->with('error', 'An unexpected error occurred while sending the reminder.');
        }
    }
}
