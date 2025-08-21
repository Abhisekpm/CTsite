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
use Illuminate\Support\Carbon;

class SendPickupReminderSMS implements ShouldQueue
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
            Log::warning("Cannot send pickup reminder SMS for order #{$this->order->id} - missing API key or phone");
            return;
        }

        try {
            $pickupDate = Carbon::parse($this->order->pickup_date)->format('l, F jS');
            $pickupTime = Carbon::parse($this->order->pickup_time)->format('h:i A');
            
            $messageBody = "Reminder: Your cake order #{$this->order->id} from Chocolate Therapy is scheduled for pickup on {$pickupDate} at {$pickupTime}. If you have any questions, please contact us at (267)541-8620.";

            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $simpleTextingApiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api-app2.simpletexting.com/v2/api/messages', [
                'contactPhone' => $customerPhone,
                'mode' => 'AUTO',
                'text' => $messageBody
            ]);

            if ($response->successful()) {
                Log::info("Pickup reminder SMS sent successfully for order #{$this->order->id}");
            } else {
                Log::error("SimpleTexting error sending pickup reminder SMS for order #{$this->order->id}: " . $response->body());
                throw new \Exception('SMS API error: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error sending pickup reminder SMS for order #{$this->order->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }
}
