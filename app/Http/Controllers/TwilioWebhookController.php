<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;
use Twilio\Security\RequestValidator; // Uncommented for request validation
use Twilio\Exceptions\SignatureException; // Add exception for validator
use Twilio\Rest\Client as TwilioClient; // Import Twilio Client
use Twilio\Exceptions\TwilioException; // Import Twilio Exception
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

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

        // --- Validate Twilio Request Signature --- 
        $validator = new RequestValidator(env('TWILIO_AUTH_TOKEN'));
        try {
            $isValid = $validator->validate(
                $request->header('X-Twilio-Signature'),
                $request->fullUrl(), // Use fullUrl() or url() depending on proxy setup
                $request->all() // Use all() for POST parameters in Laravel >= 5.3
            );
        } catch (SignatureException $e) {
            Log::error('Twilio Signature Validation Exception: ' . $e->getMessage());
            $isValid = false;
        }
        
        if (!$isValid) {
            Log::warning('Invalid Twilio Signature received.');
            return response('Invalid signature', 403);
        }
        Log::info('Twilio Signature Validated.');
        // --- End Request Validation ---

        $fromNumber = $request->input('From');
        $messageBody = trim(strtoupper($request->input('Body'))); // Trim whitespace, convert to uppercase

        if (!$fromNumber || !$messageBody) {
            Log::warning('Twilio Webhook: Missing From or Body.');
            // Still return empty TwiML to acknowledge
            $twiml = new MessagingResponse();
            return response($twiml, 200)->header('Content-Type', 'text/xml');
        }

        // --- ADD BACK: Normalize Phone Number ---
        $normalizedFromNumber = null;
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            // Assuming incoming numbers are likely US/Canada if missing country code, 
            // or rely on Twilio sending in E.164 format.
            // Default region set to US for parsing ambiguity.
            $parsedNumber = $phoneUtil->parse($fromNumber, 'US'); 
            if ($phoneUtil->isValidNumber($parsedNumber)) {
                $normalizedFromNumber = $phoneUtil->format($parsedNumber, PhoneNumberFormat::E164); // Format to +1xxxxxxxxxx
            } else {
                 Log::warning("Twilio Webhook: Could not parse/validate incoming phone number: {$fromNumber}");
                 // Use raw number as fallback or decide to reject
                 // Setting to raw number here, but the query below should ideally only match E164
                 // Consider returning an error or ignoring if validation fails strictly
                 $normalizedFromNumber = $fromNumber; 
            }
        } catch (NumberParseException $e) {
            Log::warning("Twilio Webhook: Exception parsing phone number {$fromNumber}: " . $e->getMessage());
             // Use raw number as fallback
            $normalizedFromNumber = $fromNumber;
        }
        Log::info("Twilio Webhook: Processing number {$fromNumber} (attempted normalization to {$normalizedFromNumber})");
        // --- End Normalize Phone Number ---

        // Attempt to find a 'priced' order matching the *normalized* sender's number
        // If normalization failed above and resulted in the raw number, this might not match if DB expects E164
        $order = CustomOrder::where('phone', $normalizedFromNumber) // Use normalized number
                           ->where('status', 'priced')
                           ->orderBy('created_at', 'desc') // Get the most recent priced order if multiple exist
                           ->first();

        if (!$order) {
            // Use the normalized number in the log message for clarity on what was searched
            Log::info("Twilio Webhook: No matching 'priced' order found for normalized number {$normalizedFromNumber}."); 
            // No action needed, just acknowledge receipt
        } elseif ($messageBody === 'YES') {
            // Use the normalized number in the log message
            Log::info("Twilio Webhook: Confirmation 'YES' received for order #{$order->id} from normalized number {$normalizedFromNumber}."); 
            try {
                $order->status = 'confirmed';
                $order->save();
                Log::info("Twilio Webhook: Order #{$order->id} status updated to 'confirmed'.");

                // --- Trigger Final Notifications --- 
                try {
                    $twilioSid = env('TWILIO_SID');
                    $twilioToken = env('TWILIO_TOKEN');
                    $twilioFrom = env('TWILIO_FROM');
                    // Use the normalized number that sent the confirmation for sending the reply
                    $customerPhone = $normalizedFromNumber; 
                    $adminPhone = env('ADMIN_PHONE'); // Get admin/shop phone

                    if ($twilioSid && $twilioToken && $twilioFrom && $customerPhone) {
                        $twilio = new TwilioClient($twilioSid, $twilioToken);

                        // 1. Send confirmation to Customer
                        $customerMessage = "Thanks for confirming! Your custom cake order #{$order->id} is confirmed and scheduled for pickup on {$order->pickup_date}.";
                        $twilio->messages->create($customerPhone, ['from' => $twilioFrom, 'body' => $customerMessage]);
                        Log::info("Final confirmation SMS sent to customer for order #{$order->id}.");

                        // 2. Send notification to Admin (optional)
                        if ($adminPhone) {
                             $adminMessage = "Order #{$order->id} ({$order->customer_name}) has been confirmed by the customer via SMS.";
                             $twilio->messages->create($adminPhone, ['from' => $twilioFrom, 'body' => $adminMessage]);
                             Log::info("Admin notification SMS sent for confirmed order #{$order->id}.");
                        }
                    } else {
                        Log::warning("Twilio Final Notification SMS not sent for order #{$order->id}. Missing Twilio/Customer config.");
                    }
                } catch (TwilioException $e) {
                    Log::error("TwilioException sending final confirmation SMS for order #{$order->id}: " . $e->getMessage());
                } catch (\Exception $e) { // Catch general exceptions during SMS sending
                    Log::error("General Exception sending final confirmation SMS for order #{$order->id}: " . $e->getMessage());
                }
                // --- End Final Notifications ---

            } catch (\Exception $e) {
                Log::error("Twilio Webhook: Error updating status for order #{$order->id}: " . $e->getMessage());
                // Don't let this error prevent acknowledging Twilio
            }
        } else {
            // Use the normalized number in the log message
            Log::info("Twilio Webhook: Received message '{$messageBody}' from normalized number {$normalizedFromNumber} for priced order #{$order->id} - did not match confirmation keyword."); 
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
