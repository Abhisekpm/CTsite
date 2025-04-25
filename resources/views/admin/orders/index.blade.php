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

    {{-- Future Orders Toggle --}}
    <div class="mb-3">
        @php
            // Build the base query parameters, preserving the status filter if present
            $queryParams = request()->only('status');
            $futureFilterActive = request()->query('filter') === 'future';

            // If the future filter is active, the link should remove it
            if ($futureFilterActive) {
                // No need to add 'filter' => 'future' to the toggle link
                $toggleText = 'Show All Orders';
                $toggleClass = 'btn-secondary'; 
            } else {
                // If the future filter is *not* active, the link should add it
                $queryParams['filter'] = 'future';
                $toggleText = 'Show Only Future Orders';
                $toggleClass = 'btn-outline-secondary';
            }
        @endphp
        <a href="{{ route('admin.orders.index', $queryParams) }}" 
           class="btn btn-sm {{ $toggleClass }}">
            {{ $toggleText }}
        </a>
    </div>
    {{-- End Future Orders Toggle --}}

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive responsive-table-wrapper">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Customer Name</th>
                            <th>Cake Size</th>
                            <th>Cake Flavor</th>
                            <th>Pickup Date</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-bottom">
                                <td data-label="Customer">{{ $order->customer_name }}</td>
                                <td data-label="Size">{{ $order->cake_size }}</td>
                                <td data-label="Flavor">{{ $order->cake_flavor }}</td>
                                <td data-label="Pickup">{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}</td>
                                <td data-label="Status">
                                    <span class="badge rounded-pill bg-{{ $order->status == 'pending' ? 'warning text-dark' : ($order->status == 'priced' ? 'info text-dark' : ($order->status == 'confirmed' ? 'success' : 'secondary')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td data-label="Price">{{ $order->price ? '$' . number_format($order->price, 2) : '-' }}</td>
                                <td data-label="Action">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">Details</a>
                                    {{-- Add other actions like delete if needed --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No orders found.</td>
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