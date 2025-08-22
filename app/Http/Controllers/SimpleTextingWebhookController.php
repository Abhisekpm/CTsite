<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use App\Jobs\SendCustomerOrderConfirmationSMS;

class SimpleTextingWebhookController extends Controller
{
    /**
     * Handle incoming SMS messages from SimpleTexting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        Log::info('SimpleTexting Webhook Received:', $request->all());

        // Handle SimpleTexting's actual webhook structure
        $values = $request->input('values', []);
        $fromNumber = $values['contactPhone'] ?? $request->input('contactPhone');
        $messageBody = trim(strtoupper($values['text'] ?? $request->input('text', '')));

        Log::info("SimpleTexting Webhook: Extracted data - fromNumber: {$fromNumber}, messageBody: {$messageBody}");

        if (!$fromNumber || !$messageBody) {
            Log::warning('SimpleTexting Webhook: Missing contactPhone or text.', [
                'values' => $values,
                'fromNumber' => $fromNumber,
                'messageBody' => $messageBody
            ]);
            return response()->json(['status' => 'error', 'message' => 'Missing contactPhone or text'], 400);
        }

        // Normalize phone number to E.164 format for database matching
        $normalizedPhoneNumber = null;
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $parsedNumber = $phoneUtil->parse($fromNumber, 'US'); // Assume US if no country code
            if ($phoneUtil->isValidNumber($parsedNumber)) {
                $normalizedPhoneNumber = $phoneUtil->format($parsedNumber, PhoneNumberFormat::E164);
            }
        } catch (\Exception $e) {
            Log::warning("SimpleTexting Webhook: Error normalizing phone number {$fromNumber}: " . $e->getMessage());
        }

        Log::info("SimpleTexting Webhook: Phone number normalized from {$fromNumber} to {$normalizedPhoneNumber}");

        // Try to find order using normalized phone number first, then original
        $order = null;
        if ($normalizedPhoneNumber) {
            $order = CustomOrder::where('phone', $normalizedPhoneNumber)
                               ->where('status', 'priced')
                               ->orderBy('created_at', 'desc')
                               ->first();
        }
        
        // Fallback to original number if normalized search failed
        if (!$order) {
            $order = CustomOrder::where('phone', $fromNumber)
                               ->where('status', 'priced')
                               ->orderBy('created_at', 'desc')
                               ->first();
        }

        if (!$order) {
            Log::info("SimpleTexting Webhook: No matching 'priced' order found for number {$fromNumber} (normalized: {$normalizedPhoneNumber}).");
        } elseif ($messageBody === 'YES') {
            Log::info("SimpleTexting Webhook: Confirmation 'YES' received for order #{$order->id} from number {$fromNumber}.");
            try {
                $order->status = 'confirmed';
                $order->save();
                Log::info("SimpleTexting Webhook: Order #{$order->id} status updated to 'confirmed'.");

                // Send confirmation SMS to customer
                SendCustomerOrderConfirmationSMS::dispatch($order);
                Log::info("SimpleTexting Webhook: Order confirmation SMS queued for order #{$order->id}.");

            } catch (\Exception $e) {
                Log::error("SimpleTexting Webhook: Error updating status for order #{$order->id}: " . $e->getMessage());
            }
        } else {
            Log::info("SimpleTexting Webhook: Received message '{$messageBody}' from number {$fromNumber} for priced order #{$order->id} - did not match confirmation keyword.");
        }

        return response()->json(['status' => 'success']);
    }
} 