@component('mail::message')
# Your Custom Cake Order is Priced!

Hi {{ $order->customer_name }},

Great news! We've completed the pricing for your custom cake order and it's ready for confirmation.

**Order ID:** #{{ $order->id }}<br>
**Total Price:** ${{ number_format($order->price, 2) }}<br>
**Pickup Date:** {{ \Carbon\Carbon::parse($order->pickup_date)->format('F j, Y') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}

## Payment Instructions

To confirm your order, please pay a **$20 deposit** using one of the following methods:

**Zelle:** 5179806354<br>
**Venmo:** @Nupur-Kundalia

## Confirmation Required

After making your deposit payment, please reply to confirm your order by:
- **Text/SMS:** Reply "YES" to our text message
- **Email:** Reply to this email with "CONFIRMED"
- **Phone:** Call us at (267) 541-8620

## Order Details

We'll have your custom cake ready for pickup exactly as requested. If you have any questions about your order or need to make any changes, please contact us immediately.

**Important:** Your order will be held for 24 hours pending deposit payment. After this time, we may need to release your pickup slot.

Thanks,<br>
The {{ config('app.name') }} Team

---
*Questions? Call us at (267) 541-8620 or reply to this email.*
@endcomponent