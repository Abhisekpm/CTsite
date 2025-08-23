@component('mail::message')
# Your Cake Order is Priced!

Hi {{ $order->customer_name }},

We've completed the pricing for your cake order and it's ready for confirmation.

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
- **Phone:** Call us at (267) 541-8620

## Order Details

We'll have your cake ready for pickup as requested. If you have any questions about your order or need to make any changes, please contact us immediately.

Thanks,<br>
The {{ config('app.name') }} Team

---
*Questions? Call us at (267) 541-8620*
@endcomponent