<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;
// use Twilio\Security\RequestValidator; // Optional: For request validation

class TwilioWebhookController extends Controller
{
    /**
     * Handle incoming SMS messages from Twilio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        Log::info('Twilio Webhook Received:', $request->all());

        // --- Optional: Validate Twilio Request Signature --- 
        // $validator = new RequestValidator(env('TWILIO_AUTH_TOKEN'));
        // $isValid = $validator->validate(
        //     $request->header('X-Twilio-Signature'),
        //     $request->fullUrl(),
        //     $request->toArray() // Use toArray() for POST parameters
        // );
        // if (!$isValid) {
        //     Log::warning('Invalid Twilio Signature received.');
        //     return response('Invalid signature', 403);
        // }
        // --- End Request Validation ---

        $fromNumber = $request->input('From');
        $messageBody = trim(strtoupper($request->input('Body'))); // Trim whitespace, convert to uppercase

        if (!$fromNumber || !$messageBody) {
            Log::warning('Twilio Webhook: Missing From or Body.');
            // Still return empty TwiML to acknowledge
            $twiml = new MessagingResponse();
            return response($twiml, 200)->header('Content-Type', 'text/xml');
        }

        // Attempt to find a 'priced' order matching the sender's number
        // TODO: Consider normalizing phone numbers (e.g., E.164 format) both when storing and comparing
        // For now, we assume a direct match works
        $order = CustomOrder::where('phone', $fromNumber)
                           ->where('status', 'priced')
                           ->orderBy('created_at', 'desc') // Get the most recent priced order if multiple exist
                           ->first();

        if (!$order) {
            Log::info("Twilio Webhook: No matching 'priced' order found for number {$fromNumber}.");
            // No action needed, just acknowledge receipt
        } elseif ($messageBody === 'YES') {
            Log::info("Twilio Webhook: Confirmation 'YES' received for order #{$order->id} from {$fromNumber}.");
            try {
                $order->status = 'confirmed';
                $order->save();
                Log::info("Twilio Webhook: Order #{$order->id} status updated to 'confirmed'.");

                // --- Optional: Trigger Final Notifications --- 
                // e.g., SMS to admin: "Order #{$order->id} confirmed by customer."
                // e.g., SMS to customer: "Thanks for confirming! Your order #{$order->id} is confirmed."
                // --- End Optional Notifications ---

            } catch (\Exception $e) {
                Log::error("Twilio Webhook: Error updating status for order #{$order->id}: " . $e->getMessage());
                // Don't let this error prevent acknowledging Twilio
            }
        } else {
            Log::info("Twilio Webhook: Received message '{$messageBody}' from {$fromNumber} for priced order #{$order->id} - did not match confirmation keyword.");
            // Potentially handle other replies or forward to admin?
        }

        // Respond to Twilio to acknowledge receipt and stop retries
        $twiml = new MessagingResponse();
        // Example: Optionally send a confirmation reply back
        // if ($order && $order->status === 'confirmed') { 
        //     $twiml->message('Thank you for confirming your order!');
        // }
        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }
}
