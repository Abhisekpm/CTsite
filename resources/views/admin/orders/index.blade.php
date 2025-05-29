@extends('admin.orders.layout')

@section('title', 'Cake Order List')

@section('content')
    <h2 class="mb-4">List of Cake Orders</h2>

    {{-- Status Filter Buttons --}}
    <div class="mb-3">
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

    {{-- Future Orders Toggle (Improved) --}}
    <div class="mb-3">
         <div class="btn-group btn-group-sm" role="group" aria-label="Order Date Filter">
            @php
                $statusParams = request()->only('status'); // Preserve status filter
                $currentFilter = request()->query('filter'); // Get current filter value

                // Parameters for "All Orders (including past)"
                $allTimeOrdersParams = array_merge($statusParams, ['filter' => 'all_time']);
                
                // Parameters for "Future Orders"
                $futureOrdersParams = array_merge($statusParams, ['filter' => 'future']);

                // Determine active button state for styling
                // Future is active if filter is 'future' OR if no filter is set (it's the default)
                $isFutureActive = ($currentFilter === 'future' || is_null($currentFilter));
                $isAllTimeActive = ($currentFilter === 'all_time');
            @endphp

            {{-- All Orders Button --}}
            <a href="{{ route('admin.orders.index', $allTimeOrdersParams) }}" 
               class="btn {{ $isAllTimeActive ? 'btn-primary' : 'btn-outline-primary' }}">
               All Orders
            </a>

            {{-- Future Orders Button --}}
            <a href="{{ route('admin.orders.index', $futureOrdersParams) }}" 
               class="btn {{ $isFutureActive ? 'btn-primary' : 'btn-outline-primary' }}">
               Future Orders
            </a>
        </div>
    </div>
    {{-- End Future Orders Toggle --}}

    {{-- Print Dispatch Section --}}
    <div class="mb-3 d-flex align-items-center">
        <input type="date" id="dispatch_date" class="form-control form-control-sm me-2" style="width: auto;" value="{{ now()->format('Y-m-d') }}">
        <a href="#" id="print_dispatch_btn" target="_blank" class="btn btn-info btn-sm">
            <i class="bi bi-printer"></i> Print Dispatch
        </a>
    </div>

    {{-- New Layout --}}
    @if($orders->isEmpty())
        <div class="alert alert-info text-center">No orders found.</div>
    @else
        @php
            $groupedOrders = $orders->groupBy(function ($order) {
                return \Carbon\Carbon::parse($order->pickup_date)->format('l, F j, Y');
            });
        @endphp

        @foreach ($groupedOrders as $date => $ordersOnDate)
            <div class="mb-4">
                <h4 class="mb-3" style="font-size: 1.1rem; font-weight: bold;">{{ $date }}</h4>
                <ul class="list-group">
                    @foreach ($ordersOnDate as $order)
                        <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-3">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold text-warning" style="color: #FFA500 !important;">{{ strtoupper($order->customer_name) }} (#{{ $order->id }})</div>
                                <div class="text-dark fw-bold fs-5">{{ $order->cake_flavor ?: 'N/A' }}</div>
                                <div class="text-muted" style="font-size: 0.9rem;">{{ $order->cake_size ?: 'N/A' }}</div>
                                @if($order->custom_decoration)
                                    <div class="mt-1 text-muted" style="font-size: 0.85rem; white-space: pre-wrap;">{{ $order->custom_decoration }}</div>
                                @endif
                                @if($order->message_on_cake)
                                     <div class="mt-1 text-muted" style="font-size: 0.85rem;"><em>Message: {{ $order->message_on_cake }}</em></div>
                                @endif
                                <div>
                                    <span class="badge rounded-pill bg-{{ $order->status == 'pending' ? 'warning text-dark' : ($order->status == 'priced' ? 'info text-dark' : ($order->status == 'confirmed' ? 'success' : 'secondary')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    @if($order->price)
                                        <span class="badge rounded-pill bg-success ms-1">${{ number_format($order->price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary" style="font-size: 1.2rem; padding: 0.2rem 0.5rem;">
                                <i class="bi bi-three-dots"></i> &hellip;
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        @if ($orders->hasPages())
            <div class="card-footer bg-transparent border-top-0">
                {{ $orders->links() }} {{-- Display pagination links --}}
            </div>
        @endif
    @endif
@endsection

@push('styles')
<style>
    .list-group-item {
        border-radius: 0.5rem; /* Softer edges for list items */
        border: 1px solid #e9ecef; /* Light border */
        margin-bottom: 0.75rem; /* Space between items */
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Subtle shadow */
    }
    .list-group-item .fw-bold.text-warning { /* Target the customer name specifically */
        color: #E67E22 !important; /* Using a slightly darker orange for better readability if #FFA500 is too light */
        font-size: 0.8rem; /* Smaller font size for customer name */
        margin-bottom: 0.1rem; /* Less space below customer name */
    }
    .list-group-item .text-dark.fw-bold.fs-5 { /* Target the cake flavor */
        margin-bottom: 0.1rem; /* Less space below cake flavor */
    }
    .list-group-item .text-muted {
        line-height: 1.3; /* Adjust line height for better readability in descriptions */
    }

    /* Pagination Styles */
    .pagination {
        font-size: 0.875rem; /* Smaller font size for pagination container */
    }
    .page-item .page-link {
        padding: 0.25rem 0.5rem; /* Smaller padding for page links */
        font-size: 0.875rem; /* Smaller font size for page links */
        line-height: 1.5; /* Adjust line-height if necessary */
    }
    .page-item.disabled .page-link {
        padding: 0.25rem 0.5rem; /* Ensure disabled items also have smaller padding */
    }
    /* Ensure Bootstrap Icons are loaded if you use them, e.g., via CDN in your main layout */
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dispatchDateInput = document.getElementById('dispatch_date');
        const printDispatchBtn = document.getElementById('print_dispatch_btn');
        // Base URL for the print dispatch route (will be updated with the selected date)
        // Make sure to define this route in your web.php, e.g., Route::get('admin/orders/print-dispatch/{date}', ...);
        const basePrintUrl = "{{ url('admin/orders/print-dispatch') }}";

        function updatePrintDispatchLink() {
            if (dispatchDateInput && printDispatchBtn) {
                const selectedDate = dispatchDateInput.value;
                if (selectedDate) {
                    printDispatchBtn.href = `${basePrintUrl}/${selectedDate}`;
                } else {
                    // Optionally handle case where no date is selected, e.g., disable button or revert to a default
                    printDispatchBtn.href = '#'; // Or some default link
                }
            }
        }

        if (dispatchDateInput) {
            dispatchDateInput.addEventListener('change', updatePrintDispatchLink);
        }

        // Initial setup of the link
        updatePrintDispatchLink();
    });
</script>
@endpush 