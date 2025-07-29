<?php

namespace App\Http\Controllers;

use App\Models\CustomOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        $fromNumber = $request->input('contactPhone');
        $messageBody = trim(strtoupper($request->input('text')));

        if (!$fromNumber || !$messageBody) {
            Log::warning('SimpleTexting Webhook: Missing contactPhone or text.');
            return response()->json(['status' => 'error', 'message' => 'Missing contactPhone or text'], 400);
        }

        $order = CustomOrder::where('phone', $fromNumber)
                           ->where('status', 'priced')
                           ->orderBy('created_at', 'desc')
                           ->first();

        if (!$order) {
            Log::info("SimpleTexting Webhook: No matching 'priced' order found for number {$fromNumber}.");
        } elseif ($messageBody === 'YES') {
            Log::info("SimpleTexting Webhook: Confirmation 'YES' received for order #{$order->id} from number {$fromNumber}.");
            try {
                $order->status = 'confirmed';
                $order->save();
                Log::info("SimpleTexting Webhook: Order #{$order->id} status updated to 'confirmed'.");

            } catch (\Exception $e) {
                Log::error("SimpleTexting Webhook: Error updating status for order #{$order->id}: " . $e->getMessage());
            }
        } else {
            Log::info("SimpleTexting Webhook: Received message '{$messageBody}' from number {$fromNumber} for priced order #{$order->id} - did not match confirmation keyword.");
        }

        return response()->json(['status' => 'success']);
    }
} 