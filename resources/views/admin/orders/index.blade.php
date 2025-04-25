@extends('admin.orders.layout')

@section('title', 'Custom Order List')

@section('content')
    <h2 class="mb-4">Custom Cake Orders</h2>

    {{-- Status Filter Buttons --}}
    <div class="mb-3">
        <span class="me-2">Filter by status:</span>
        <div class="btn-group btn-group-sm" role="group" aria-label="Order Status Filter">
            <a href="{{ route('admin.orders.index') }}" 
               class="btn {{ !$currentStatus ? 'btn-primary' : 'btn-outline-primary' }}">
                All
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
               class="btn {{ $currentStatus == 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                Pending
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'priced']) }}" 
               class="btn {{ $currentStatus == 'priced' ? 'btn-primary' : 'btn-outline-primary' }}">
                Priced
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" 
               class="btn {{ $currentStatus == 'confirmed' ? 'btn-primary' : 'btn-outline-primary' }}">
                Confirmed
            </a>
            {{-- Add other statuses like 'cancelled' if needed using the same pattern --}}
        </div>
    </div>
    {{-- End Status Filter Buttons --}}

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive responsive-table-wrapper">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Pickup Date</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td data-label="ID">#{{ $order->id }}</td>
                                <td data-label="Customer">{{ $order->customer_name }}</td>
                                <td data-label="Phone">{{ $order->phone }}</td>
                                <td data-label="Pickup">{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}</td>
                                <td data-label="Status">
                                    <span class="badge rounded-pill bg-{{ $order->status == 'pending' ? 'warning text-dark' : ($order->status == 'priced' ? 'info text-dark' : ($order->status == 'confirmed' ? 'success' : 'secondary')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td data-label="Price">{{ $order->price ? '$' . number_format($order->price, 2) : '-' }}</td>
                                <td data-label="Submitted">{{ $order->created_at->diffForHumans() }}</td>
                                <td data-label="Action">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">View/Price</a>
                                    {{-- Add other actions like delete if needed --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }} {{-- Display pagination links --}}
            </div>
        @endif
    </div>
@endsection 