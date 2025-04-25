<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Twilio\Rest\Client as TwilioClient; // Import Twilio Client
use Twilio\Exceptions\TwilioException; // Import Twilio Exception
use Exception; // Import base Exception

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

        // Start query builder
        $query = CustomOrder::orderBy('created_at', 'desc');

        // Apply status filter if a valid status is provided
        if ($status && in_array($status, $validStatuses)) {
            $query->where('status', $status);
        }

        // Fetch orders with pagination
        $orders = $query->paginate(20)->withQueryString(); // Append query string to pagination links

        // Pass orders and current status filter to the view
        return view('admin.orders.index', [
            'orders' => $orders,
            'currentStatus' => $status // Pass the current filter value
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
}
