@component('mail::message')
# Your Custom Cake Request is Received!

Hi {{ $order->customer_name }},

Thank you for placing a custom cake order with us. This email confirms that we have received your request and it is now pending review.

**Order ID:** #{{ $order->id }}<br>
**Pickup Date:** {{ \Carbon\Carbon::parse($order->pickup_date)->format('F j, Y') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}

We are reviewing your cake details and will contact you shortly with the final pricing and to confirm everything.

If you have any immediate questions, feel free to reply to this email or call us.

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent 