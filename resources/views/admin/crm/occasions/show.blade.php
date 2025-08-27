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
                        <h3 class="page-title">Occasion: {{ ucfirst($occasion->occasion_type) }}
                            @if($occasion->honoree_name)
                                - {{ $occasion->honoree_name }}
                            @endif
                        </h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}">CRM</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.occasions') }}">Occasions</a></li>
                            <li class="breadcrumb-item active">{{ ucfirst($occasion->occasion_type) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Occasion Overview Cards --}}
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                        <h4>{{ $occasion->history_count }}</h4>
                        <small>Orders for this Occasion</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-info">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-info mb-2"></i>
                        <h4>{{ $occasion->anchor_confidence === 'high' ? 'High' : ($occasion->anchor_confidence === 'medium' ? 'Medium' : 'Low') }}</h4>
                        <small>Confidence Level</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-success">
                    <div class="card-body text-center">
                        <i class="fas fa-bell fa-2x text-success mb-2"></i>
                        <h4>{{ $occasion->reminder_date ? $occasion->reminder_date->format('M d') : 'Not Set' }}</h4>
                        <small>Reminder Date</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-light-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-week fa-2x text-warning mb-2"></i>
                        <h4>{{ $occasion->next_anchor_week_start ? $occasion->next_anchor_week_start->diffInDays(now()) : 'N/A' }}</h4>
                        <small>Days Until Next Occurrence</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            {{-- Occasion Details --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Occasion Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="40%">Occasion Type:</th>
                                    <td><span class="badge badge-primary">{{ ucfirst($occasion->occasion_type) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Honoree Name:</th>
                                    <td>{{ $occasion->honoree_name ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Anchor Week:</th>
                                    <td>
                                        {{ $occasion->anchor_week_start_date->format('M d') }} - 
                                        {{ $occasion->anchor_week_start_date->endOfWeek()->format('M d, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Next Occurrence:</th>
                                    <td>
                                        @if($occasion->next_anchor_week_start)
                                            {{ $occasion->next_anchor_week_start->format('M d, Y') }}
                                            <br><small class="text-muted">{{ $occasion->next_anchor_week_start->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Not calculated</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Reminder Date:</th>
                                    <td>
                                        @if($occasion->reminder_date)
                                            {{ $occasion->reminder_date->format('M d, Y') }}
                                            <br><small class="text-muted">{{ $occasion->reminder_date->diffForHumans() }}</small>
                                            @if($occasion->reminder_sent)
                                                <br><span class="badge badge-success">Reminder Sent</span>
                                            @elseif($occasion->reminder_date->isPast())
                                                <br><span class="badge badge-danger">Overdue</span>
                                            @else
                                                <br><span class="badge badge-info">Pending</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Confidence Level:</th>
                                    <td>
                                        @if($occasion->anchor_confidence === 'high')
                                            <span class="badge badge-success">High Confidence</span>
                                        @elseif($occasion->anchor_confidence === 'medium')
                                            <span class="badge badge-warning">Medium Confidence</span>
                                        @else
                                            <span class="badge badge-secondary">Low Confidence</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Order History:</th>
                                    <td>
                                        <span class="badge badge-info">{{ $occasion->history_count }}</span> total orders
                                        @if($occasion->history_years)
                                            <br><small class="text-muted">Years: {{ $occasion->history_years }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Order Date:</th>
                                    <td>
                                        @if($occasion->last_order_date_latest)
                                            {{ $occasion->last_order_date_latest->format('M d, Y') }}
                                            <br><small class="text-muted">{{ $occasion->last_order_date_latest->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">No orders yet</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $occasion->customer->buyer_name }}
                                    @if($occasion->customer->orders_count >= 5)
                                        <i class="fas fa-crown text-warning ms-1" title="High Value Customer"></i>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $occasion->customer->primary_email }}</small>
                            </div>
                            <div>
                                <a href="{{ route('admin.crm.customers.show', $occasion->customer) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-user"></i> View Customer
                                </a>
                            </div>
                        </div>
                        
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <th width="40%">Customer ID:</th>
                                    <td><code>{{ $occasion->customer->customer_id }}</code></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $occasion->customer->primary_phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Total Orders:</th>
                                    <td><span class="badge badge-primary">{{ $occasion->customer->orders_count }}</span></td>
                                </tr>
                                <tr>
                                    <th>Marketing Opt-in:</th>
                                    <td>
                                        @if($occasion->customer->marketing_opt_in)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>First Order:</th>
                                    <td>{{ $occasion->customer->first_order ? $occasion->customer->first_order->format('M d, Y') : 'No orders yet' }}</td>
                                </tr>
                                <tr>
                                    <th>Last Order:</th>
                                    <td>{{ $occasion->customer->last_order ? $occasion->customer->last_order->format('M d, Y') : 'No orders yet' }}</td>
                                </tr>
                                @if($occasion->customer->allergens)
                                <tr>
                                    <th>Allergies:</th>
                                    <td><span class="badge badge-warning">{{ $occasion->customer->allergens }}</span></td>
                                </tr>
                                @endif
                                @if($occasion->customer->fav_flavors)
                                <tr>
                                    <th>Favorite Flavors:</th>
                                    <td><small>{{ $occasion->customer->fav_flavors }}</small></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Other Customer Occasions --}}
        @if($occasion->customer->occasions->where('id', '!=', $occasion->id)->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Other Customer Occasions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Honoree</th>
                                        <th>Anchor Week</th>
                                        <th>Next Occurrence</th>
                                        <th>History</th>
                                        <th>Confidence</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($occasion->customer->occasions->where('id', '!=', $occasion->id) as $otherOccasion)
                                    <tr>
                                        <td><span class="badge badge-primary">{{ ucfirst($otherOccasion->occasion_type) }}</span></td>
                                        <td>{{ $otherOccasion->honoree_name ?? 'Not specified' }}</td>
                                        <td>
                                            {{ $otherOccasion->anchor_week_start_date->format('M d') }} - 
                                            {{ $otherOccasion->anchor_week_start_date->endOfWeek()->format('M d') }}
                                        </td>
                                        <td>
                                            @if($otherOccasion->next_anchor_week_start)
                                                {{ $otherOccasion->next_anchor_week_start->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">Not calculated</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-info">{{ $otherOccasion->history_count }}</span></td>
                                        <td>
                                            @if($otherOccasion->anchor_confidence === 'high')
                                                <span class="badge badge-success">High</span>
                                            @elseif($otherOccasion->anchor_confidence === 'medium')
                                                <span class="badge badge-warning">Medium</span>
                                            @else
                                                <span class="badge badge-secondary">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.crm.occasions.show', $otherOccasion) }}" 
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

        {{-- Related Orders (if we had historical order data) --}}
        @if($occasion->customer->customOrders->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Customer Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Order Date</th>
                                        <th>Cake Type</th>
                                        <th>Size</th>
                                        <th>Pickup Date</th>
                                        <th>Status</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($occasion->customer->customOrders->take(5) as $order)
                                    <tr>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>{{ $order->cake_type ?? 'Custom' }}</td>
                                        <td>{{ $order->cake_size ?? 'Not specified' }}</td>
                                        <td>{{ $order->pickup_date ? $order->pickup_date->format('M d, Y') : 'Not set' }}</td>
                                        <td>
                                            @if($order->status === 'confirmed')
                                                <span class="badge badge-success">Confirmed</span>
                                            @elseif($order->status === 'priced')
                                                <span class="badge badge-info">Priced</span>
                                            @elseif($order->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($order->status ?? 'unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->price)
                                                ${{ number_format($order->price, 2) }}
                                            @else
                                                <span class="text-muted">Not priced</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($occasion->customer->customOrders->count() > 5)
                        <div class="text-center mt-3">
                            <small class="text-muted">Showing latest 5 orders of {{ $occasion->customer->customOrders->count() }} total</small>
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
                <a href="{{ route('admin.crm.occasions') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Occasions
                </a>
                <a href="{{ route('admin.crm.occasions', ['view' => 'weekly']) }}" class="btn btn-info">
                    <i class="fas fa-calendar-week"></i> Weekly View
                </a>
                <a href="{{ route('admin.crm.customers.show', $occasion->customer) }}" class="btn btn-primary">
                    <i class="fas fa-user"></i> View Customer Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection