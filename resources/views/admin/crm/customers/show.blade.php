@extends('admin.layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <h3 class="page-title">Customer: {{ $customer->buyer_name }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}">CRM</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.customers') }}">Customers</a></li>
                            <li class="breadcrumb-item active">{{ $customer->buyer_name }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Overview Cards --}}
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-info">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-2x text-info mb-2"></i>
                        <h4>{{ $customer->orders_count }}</h4>
                        <small>Total Orders</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-success">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                        <h4>{{ $customer->occasions->count() }}</h4>
                        <small>Total Occasions</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h4>{{ $insights['days_since_last_order'] ?? 'N/A' }}</h4>
                        <small>Days Since Last Order</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-2x text-danger mb-2"></i>
                        <h4>{{ $insights['average_order_frequency'] ?? 'N/A' }}</h4>
                        <small>Avg Days Between Orders</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            {{-- Customer Details --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td>{{ $customer->buyer_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $customer->primary_email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $customer->primary_phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Customer ID:</th>
                                    <td><code>{{ $customer->customer_id }}</code></td>
                                </tr>
                                <tr>
                                    <th>Marketing Opt-in:</th>
                                    <td>
                                        @if($customer->marketing_opt_in)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Channel Preference:</th>
                                    <td>{{ $customer->channel_preference ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>First Order:</th>
                                    <td>{{ $customer->first_order ? \Carbon\Carbon::parse($customer->first_order)->format('M d, Y') : 'No orders yet' }}</td>
                                </tr>
                                <tr>
                                    <th>Last Order:</th>
                                    <td>{{ $customer->last_order ? \Carbon\Carbon::parse($customer->last_order)->format('M d, Y') : 'No orders yet' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Preferences and Notes --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Preferences & Dietary Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><strong>Favorite Flavors:</strong></label>
                            <p class="text-muted">{{ $customer->fav_flavors ?? 'Not specified' }}</p>
                        </div>
                        <div class="form-group">
                            <label><strong>Eggs OK:</strong></label>
                            <p>
                                @if($customer->eggs_ok === 'Yes')
                                    <span class="badge badge-success">Yes</span>
                                @elseif($customer->eggs_ok === 'No')
                                    <span class="badge badge-danger">No</span>
                                @else
                                    <span class="badge badge-secondary">Not specified</span>
                                @endif
                            </p>
                        </div>
                        <div class="form-group">
                            <label><strong>Allergens:</strong></label>
                            <p class="text-muted">{{ $customer->allergens ?? 'None specified' }}</p>
                        </div>
                        <div class="form-group">
                            <label><strong>Notes:</strong></label>
                            <p class="text-muted">{{ $customer->notes ?? 'No notes' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Occasions --}}
        @if($insights['upcoming_occasions']->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Upcoming Occasions (Next 3 Months)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Occasion Type</th>
                                        <th>Honoree</th>
                                        <th>Week</th>
                                        <th>Reminder Date</th>
                                        <th>Confidence</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($insights['upcoming_occasions'] as $occasion)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ ucfirst($occasion->occasion_type) }}</span>
                                        </td>
                                        <td>{{ $occasion->honoree_name ?? 'Not specified' }}</td>
                                        <td>
                                            {{ $occasion->next_anchor_week_start ? \Carbon\Carbon::parse($occasion->next_anchor_week_start)->format('M d') : 'TBD' }} - 
                                            {{ $occasion->next_anchor_week_start ? \Carbon\Carbon::parse($occasion->next_anchor_week_start)->endOfWeek()->format('M d, Y') : 'TBD' }}
                                        </td>
                                        <td>
                                            {{ $occasion->reminder_date ? \Carbon\Carbon::parse($occasion->reminder_date)->format('M d, Y') : 'TBD' }}
                                            <br><small class="text-muted">{{ $occasion->reminder_date ? \Carbon\Carbon::parse($occasion->reminder_date)->diffForHumans() : '' }}</small>
                                        </td>
                                        <td>
                                            @if($occasion->anchor_confidence === 'high')
                                                <span class="badge badge-success">High</span>
                                            @elseif($occasion->anchor_confidence === 'medium')
                                                <span class="badge badge-warning">Medium</span>
                                            @else
                                                <span class="badge badge-secondary">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.crm.occasions.show', $occasion) }}" 
                                               class="btn btn-sm btn-outline-info">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- All Customer Occasions --}}
        @if($customer->occasions->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">All Customer Occasions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Honoree</th>
                                        <th>Anchor Week</th>
                                        <th>Next Occurrence</th>
                                        <th>History</th>
                                        <th>Last Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->occasions as $occasion)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ ucfirst($occasion->occasion_type) }}</span>
                                        </td>
                                        <td>{{ $occasion->honoree_name ?? 'Not specified' }}</td>
                                        <td>
                                            {{ $occasion->anchor_week_start_date ? \Carbon\Carbon::parse($occasion->anchor_week_start_date)->format('M d') : 'TBD' }} - 
                                            {{ $occasion->anchor_week_start_date ? \Carbon\Carbon::parse($occasion->anchor_week_start_date)->endOfWeek()->format('M d') : 'TBD' }}
                                        </td>
                                        <td>
                                            @if($occasion->next_anticipated_order_date)
                                                {{ \Carbon\Carbon::parse($occasion->next_anticipated_order_date)->format('M d, Y') }}
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($occasion->next_anticipated_order_date)->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">Not calculated</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $occasion->history_count }}</span> orders
                                            @if($occasion->history_years)
                                                <br><small class="text-muted">{{ $occasion->history_years }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($occasion->last_order_date_latest)
                                                {{ \Carbon\Carbon::parse($occasion->last_order_date_latest)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.crm.occasions.show', $occasion) }}" 
                                               class="btn btn-sm btn-outline-info">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Confirmed Order History --}}
        @php
            $confirmedOrders = $customer->customOrders->where('status', 'confirmed');
        @endphp
        @if($confirmedOrders->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Confirmed Order History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order Date</th>
                                        <th>Pickup Date</th>
                                        <th>Cake Type</th>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($confirmedOrders->sortByDesc('pickup_date')->take(10) as $order)
                                    <tr>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') : 'Not set' }}
                                            @if($order->pickup_date)
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($order->pickup_date)->format('D') }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $order->cake_type ?? 'Custom' }}</td>
                                        <td>{{ $order->cake_size ?? 'Not specified' }}</td>
                                        <td>
                                            @if($order->price)
                                                <span class="badge badge-success">${{ number_format($order->price, 2) }}</span>
                                            @else
                                                <span class="text-muted">Not priced</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="btn btn-sm btn-outline-primary">View Order</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($confirmedOrders->count() > 10)
                        <div class="text-center mt-3">
                            <small class="text-muted">Showing latest 10 confirmed orders of {{ $confirmedOrders->count() }} total</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{ route('admin.crm.customers') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Customers
                </a>
                <a href="{{ route('admin.crm.occasions', ['customer_id' => $customer->customer_id]) }}" class="btn btn-info">
                    <i class="fas fa-calendar"></i> View Customer Occasions
                </a>
                @if($customer->customOrders->count() > 10)
                <a href="{{ route('admin.orders.index', ['customer' => $customer->primary_email]) }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> View All Orders
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection