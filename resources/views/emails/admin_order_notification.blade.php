@component('mail::message')
# New Custom Cake Request Received

A new custom cake request has been submitted and requires pricing.

**Order ID:** #{{ $order->id }}
**Customer Name:** {{ $order->customer_name }}
**Customer Email:** {{ $order->email }}
**Customer Phone:** {{ $order->phone }}

**Pickup Date:** {{ \Carbon\Carbon::parse($order->pickup_date)->format('F j, Y') }}
**Pickup Time:** {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}

You can view the full order details in the admin panel.

@component('mail::button', ['url' => url('/admin/orders/' . $order->id)])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 