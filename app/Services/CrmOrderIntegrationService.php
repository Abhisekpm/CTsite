<?php

namespace App\Services;

use App\Models\CustomOrder;
use App\Models\CrmCustomer;
use App\Models\CrmOccasion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CrmOrderIntegrationService
{
    /**
     * Process a confirmed order and update CRM data
     *
     * @param CustomOrder $order
     * @return void
     */
    public function processConfirmedOrder(CustomOrder $order): void
    {
        try {
            Log::info("CRM Integration: Processing confirmed order #{$order->id}");
            
            // Step 1: Find or create customer
            $customer = $this->findOrCreateCustomer($order);
            
            // Step 2: Update customer order metrics
            $this->updateCustomerMetrics($customer, $order);
            
            // Step 3: Process occasion logic
            $this->processOccasionLogic($customer, $order);
            
            Log::info("CRM Integration: Successfully processed order #{$order->id} for customer {$customer->customer_id}");
            
        } catch (\Exception $e) {
            Log::error("CRM Integration: Failed to process order #{$order->id}: " . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e
            ]);
            // Don't throw - we don't want to break order confirmation
        }
    }

    /**
     * Find existing customer or create new one
     *
     * @param CustomOrder $order
     * @return CrmCustomer
     */
    private function findOrCreateCustomer(CustomOrder $order): CrmCustomer
    {
        $customerId = strtolower($order->email); // Use email as customer_id
        
        $customer = CrmCustomer::where('customer_id', $customerId)->first();
        
        if (!$customer) {
            Log::info("CRM Integration: Creating new customer for email: {$order->email}");
            
            $customer = CrmCustomer::create([
                'customer_id' => $customerId,
                'buyer_name' => $order->customer_name,
                'primary_email' => $order->email,
                'primary_phone' => $order->phone,
                'orders_count' => 0,
                'first_order' => null,
                'last_order' => null,
                'fav_flavors' => null,
                'allergens' => $order->allergies,
                'marketing_opt_in' => $order->sms_consent ?? false,
            ]);
        } else {
            Log::info("CRM Integration: Found existing customer: {$customer->customer_id}");
            
            // Update customer info if changed
            $customer->update([
                'buyer_name' => $order->customer_name, // Update name if different
                'primary_phone' => $order->phone,      // Update phone if different  
                'allergens' => $order->allergies ?: $customer->allergens,
                'marketing_opt_in' => $order->sms_consent ?? $customer->marketing_opt_in,
            ]);
        }
        
        return $customer;
    }

    /**
     * Update customer order metrics and preferences
     *
     * @param CrmCustomer $customer
     * @param CustomOrder $order
     * @return void
     */
    private function updateCustomerMetrics(CrmCustomer $customer, CustomOrder $order): void
    {
        $orderDate = Carbon::parse($order->pickup_date);
        
        // Update order count
        $customer->orders_count += 1;
        
        // Update first order date
        if (!$customer->first_order) {
            $customer->first_order = $orderDate;
        }
        
        // Update last order date
        $customer->last_order = $orderDate;
        
        // Update favorite flavors (simple concatenation for now)
        if ($order->cake_flavor) {
            $existingFlavors = $customer->fav_flavors ? explode(', ', $customer->fav_flavors) : [];
            if (!in_array($order->cake_flavor, $existingFlavors)) {
                $existingFlavors[] = $order->cake_flavor;
                $customer->fav_flavors = implode(', ', array_slice($existingFlavors, -5)); // Keep last 5
            }
        }
        
        $customer->save();
        
        Log::info("CRM Integration: Updated customer metrics", [
            'customer_id' => $customer->customer_id,
            'orders_count' => $customer->orders_count,
            'last_order' => $customer->last_order
        ]);
    }

    /**
     * Process occasion detection and update logic
     *
     * @param CrmCustomer $customer
     * @param CustomOrder $order
     * @return void
     */
    private function processOccasionLogic(CrmCustomer $customer, CustomOrder $order): void
    {
        // Only process if we can determine an occasion type
        $occasionType = $this->detectOccasionType($order);
        
        if (!$occasionType) {
            Log::info("CRM Integration: No occasion type detected for order #{$order->id}");
            return;
        }
        
        $orderDate = Carbon::parse($order->pickup_date);
        
        // Look for existing occasion of the same type
        $existingOccasion = $customer->occasions()
            ->where('occasion_type', $occasionType)
            ->first();
        
        if ($existingOccasion) {
            Log::info("CRM Integration: Updating existing {$occasionType} occasion for customer {$customer->customer_id}");
            $this->updateExistingOccasion($existingOccasion, $order, $orderDate);
        } else {
            Log::info("CRM Integration: Creating new {$occasionType} occasion for customer {$customer->customer_id}");
            $this->createNewOccasion($customer, $occasionType, $order, $orderDate);
        }
    }

    /**
     * Detect occasion type from order details
     *
     * @param CustomOrder $order
     * @return string|null
     */
    private function detectOccasionType(CustomOrder $order): ?string
    {
        $messageOnCake = strtolower($order->message_on_cake ?? '');
        $customDecoration = strtolower($order->custom_decoration ?? '');
        $combinedText = $messageOnCake . ' ' . $customDecoration;
        
        // Simple keyword detection
        if (preg_match('/\b(happy birthday|birthday|bday)\b/i', $combinedText)) {
            return 'birthday';
        }
        
        if (preg_match('/\b(anniversary|anni)\b/i', $combinedText)) {
            return 'anniversary';
        }
        
        if (preg_match('/\b(graduation|graduate|grad)\b/i', $combinedText)) {
            return 'graduation';
        }
        
        if (preg_match('/\b(baby shower|shower)\b/i', $combinedText)) {
            return 'baby_shower';
        }
        
        if (preg_match('/\b(gender reveal)\b/i', $combinedText)) {
            return 'gender_reveal';
        }
        
        return null; // No occasion detected
    }

    /**
     * Update an existing occasion with new order data
     *
     * @param CrmOccasion $occasion
     * @param CustomOrder $order
     * @param Carbon $orderDate
     * @return void
     */
    private function updateExistingOccasion(CrmOccasion $occasion, CustomOrder $order, Carbon $orderDate): void
    {
        // Update last order date and increment history count
        $currentLastOrder = $occasion->last_order_date_latest ? 
            Carbon::parse($occasion->last_order_date_latest) : null;
        
        if (!$currentLastOrder || $orderDate->gt($currentLastOrder)) {
            $occasion->last_order_date_latest = $orderDate;
        }
        
        $occasion->history_count += 1;
        
        // Update honoree name if we can extract it and it's not set
        if (!$occasion->honoree_name) {
            $occasion->honoree_name = $this->extractHonoreeName($order);
        }
        
        // Update history years - add current year if not already included
        if ($occasion->history_years) {
            $years = explode(',', $occasion->history_years);
            $currentYear = $orderDate->year;
            if (!in_array($currentYear, $years)) {
                $years[] = $currentYear;
                $occasion->history_years = implode(',', array_unique($years));
            }
        } else {
            $occasion->history_years = $orderDate->year;
        }
        
        // Recalculate dates using the NEW logic
        $occasion->next_anticipated_order_date = null; // Force recalculation
        $occasion->updateCalculatedDates();
        
        $occasion->save();
        
        Log::info("CRM Integration: Updated existing occasion", [
            'occasion_id' => $occasion->id,
            'type' => $occasion->occasion_type,
            'history_count' => $occasion->history_count,
            'next_anticipated' => $occasion->next_anticipated_order_date
        ]);
    }

    /**
     * Create a new occasion for the customer
     *
     * @param CrmCustomer $customer
     * @param string $occasionType
     * @param CustomOrder $order
     * @param Carbon $orderDate
     * @return void
     */
    private function createNewOccasion(CrmCustomer $customer, string $occasionType, CustomOrder $order, Carbon $orderDate): void
    {
        $occasion = new CrmOccasion([
            'customer_id' => $customer->customer_id,
            'occasion_type' => $occasionType,
            'honoree_name' => $this->extractHonoreeName($order),
            'anchor_confidence' => 'medium', // First occurrence = medium confidence
            'last_order_date_latest' => $orderDate,
            'history_count' => 1,
            'history_years' => $orderDate->year,
        ]);
        
        // Calculate all the dates using the model's logic
        $occasion->updateCalculatedDates();
        
        $occasion->save();
        
        Log::info("CRM Integration: Created new occasion", [
            'occasion_id' => $occasion->id,
            'customer_id' => $customer->customer_id,
            'type' => $occasionType,
            'anchor_week' => $occasion->anchor_week_start_date,
            'reminder_date' => $occasion->reminder_date
        ]);
    }

    /**
     * Extract honoree name from order message
     *
     * @param CustomOrder $order
     * @return string|null
     */
    private function extractHonoreeName(CustomOrder $order): ?string
    {
        $message = $order->message_on_cake ?? '';
        
        // Simple name extraction patterns
        if (preg_match('/happy birthday\s+([A-Za-z\s]+)/i', $message, $matches)) {
            return trim($matches[1]);
        }
        
        if (preg_match('/([A-Za-z\s]+)\'s birthday/i', $message, $matches)) {
            return trim($matches[1]);
        }
        
        if (preg_match('/happy anniversary\s+([A-Za-z\s]+)/i', $message, $matches)) {
            return trim($matches[1]);
        }
        
        return null;
    }
}