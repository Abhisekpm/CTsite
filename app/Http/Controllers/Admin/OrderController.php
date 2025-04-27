<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Added Carbon for date comparison
use Twilio\Rest\Client as TwilioClient; // Import Twilio Client
use Twilio\Exceptions\TwilioException; // Import Twilio Exception
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

        // Apply future orders filter if requested
        if ($filter === 'future') {
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
                    $twilioSid = env('TWILIO_SID');
                    $twilioToken = env('TWILIO_TOKEN');
                    $twilioFrom = env('TWILIO_FROM');
                    $customerPhone = $order->phone; // Ensure E.164 format if possible

                    if ($twilioSid && $twilioToken && $twilioFrom && $customerPhone) {
                        $twilio = new TwilioClient($twilioSid, $twilioToken);

                        $formattedPrice = number_format($order->price, 2);
                        $shopPhone = env('ADMIN_PHONE', 'our shop'); // Get admin/shop phone or default

                        $messageBody = "Your custom cake order #{$order->id} is priced at $${formattedPrice}. Please pay $20 via Zelle or Venmo and reply YES to confirm, or contact {$shopPhone} with questions.";

                        $twilio->messages->create(
                            $customerPhone, // To customer
                            [
                                'from' => $twilioFrom,
                                'body' => $messageBody
                            ]
                        );
                        $smsSent = true;
                        logger()->info("Pricing SMS sent for order #{$order->id}.");

                    } else {
                         $smsError = 'Twilio SMS not sent. Missing Twilio config in .env';
                         logger()->warning('Twilio SMS not sent for order #' . $order->id . '. Missing Twilio/Customer config.');
                    }
                } catch (TwilioException $e) {
                    $smsError = 'Twilio Error: ' . $e->getMessage();
                    logger()->error('TwilioException sending pricing SMS for order #' . $order->id . ': ' . $e->getMessage());
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
            'customer_name' => 'sometimes|string|max:255', // Changed required to sometimes
            'cake_size' => 'sometimes|string|max:100',        // Changed required to sometimes
            'cake_flavor' => 'sometimes|string|max:100',     // Changed required to sometimes
            'cake_sponge' => 'nullable|string|max:100',        // Added cake sponge validation
            'eggs_ok' => ['sometimes', Rule::in(['Yes', 'No'])], // Changed required to sometimes
            'message_on_cake' => 'nullable|string|max:500',
            'allergies' => 'nullable|string|max:500',
            'custom_decoration' => 'nullable|string|max:1000',
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
    public function confirmOrder(CustomOrder $order)
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
    public function cancelOrder(CustomOrder $order)
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
}
