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

class SendAdminOrderSMS implements ShouldQueue
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
        $adminPhone = env('ADMIN_PHONE');
        $simpleTextingApiKey = env('SIMPLETEXTING_API_KEY');

        if (!$adminPhone || !$simpleTextingApiKey) {
            Log::warning("Cannot send admin SMS for order #{$this->order->id} - missing admin phone or API key");
            return;
        }

        try {
            $adminMessage = "New cake request (#{$this->order->id}) from {$this->order->customer_name} for pickup on {$this->order->pickup_date}. Needs pricing.";
            
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $simpleTextingApiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api-app2.simpletexting.com/v2/api/messages', [
                'contactPhone' => $adminPhone,
                'mode' => 'AUTO',
                'text' => $adminMessage
            ]);

            if ($response->successful()) {
                Log::info("Admin order SMS sent successfully for order #{$this->order->id}");
            } else {
                Log::error("SimpleTexting error sending admin SMS for order #{$this->order->id}: " . $response->body());
                throw new \Exception('SMS API error: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error sending admin SMS for order #{$this->order->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger retry
        }
    }
}
