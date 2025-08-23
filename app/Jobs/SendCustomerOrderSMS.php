<?php

namespace App\Jobs;

use App\Models\CustomOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendCustomerOrderSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * The order instance.
     *
     * @var \App\Models\CustomOrder
     */
    protected $order;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\CustomOrder $order
     * @return void
     */
    public function __construct(CustomOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Only send SMS if customer consented
        if (!$this->order->sms_consent) {
            Log::info("Skipping customer SMS for order #{$this->order->id} - no SMS consent");
            return;
        }

        $simpleTextingApiKey = env('SIMPLETEXTING_API_KEY');
        $customerPhone = $this->order->phone;

        if (!$simpleTextingApiKey || !$customerPhone) {
            Log::warning("Cannot send customer SMS for order #{$this->order->id} - missing API key or phone");
            return;
        }

        try {
            $customerMessage = "Thanks, {$this->order->customer_name}! Your cake request (#{$this->order->id}) is received and pending review. We'll text you with pricing soon.";
            
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $simpleTextingApiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api-app2.simpletexting.com/v2/api/messages', [
                'contactPhone' => $customerPhone,
                'mode' => 'AUTO',
                'text' => $customerMessage
            ]);

            if ($response->successful()) {
                Log::info("Customer order SMS sent successfully for order #{$this->order->id}");
            } else {
                Log::error("SimpleTexting error sending customer SMS for order #{$this->order->id}: " . $response->body());
                throw new \Exception('SMS API error: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error sending customer SMS for order #{$this->order->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }
}
