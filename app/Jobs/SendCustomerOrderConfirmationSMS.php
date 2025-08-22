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

class SendCustomerOrderConfirmationSMS implements ShouldQueue
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
        $simpleTextingApiKey = env('SIMPLETEXTING_API_KEY');
        $customerPhone = $this->order->phone;

        if (!$simpleTextingApiKey || !$customerPhone) {
            Log::warning("Cannot send confirmation SMS for order #{$this->order->id} - missing API key or phone");
            return;
        }

        try {
            $pickupDate = \Carbon\Carbon::parse($this->order->pickup_date)->format('l, F jS');
            $pickupTime = \Carbon\Carbon::parse($this->order->pickup_time)->format('h:i A');
            
            $messageBody = "Great! Your cake order #{$this->order->id} is CONFIRMED for pickup on {$pickupDate} at {$pickupTime}. We'll start working on your custom cake. Contact us at (267)-541-8620 if you have any questions.";

            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $simpleTextingApiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api-app2.simpletexting.com/v2/api/messages', [
                'contactPhone' => $customerPhone,
                'mode' => 'AUTO',
                'text' => $messageBody
            ]);

            if ($response->successful()) {
                Log::info("Order confirmation SMS sent successfully for order #{$this->order->id}");
            } else {
                Log::error("SimpleTexting error sending confirmation SMS for order #{$this->order->id}: " . $response->body());
                throw new \Exception('SMS API error: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error sending confirmation SMS for order #{$this->order->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }
}