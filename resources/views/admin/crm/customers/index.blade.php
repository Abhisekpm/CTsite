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
                        <h3 class="page-title">CRM Customers</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}">CRM</a></li>
                            <li class="breadcrumb-item active">Customers</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters and Search --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Customer Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.crm.customers') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search Customers</label>
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Name, email, or phone">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Customer Segment</label>
                                <select name="segment" class="form-control">
                                    <option value="">All Customers</option>
                                    <option value="high_value" {{ request('segment') == 'high_value' ? 'selected' : '' }}>High Value (5+ orders)</option>
                                    <option value="recent" {{ request('segment') == 'recent' ? 'selected' : '' }}>Recent (6 months)</option>
                                    <option value="inactive" {{ request('segment') == 'inactive' ? 'selected' : '' }}>Inactive (6+ months)</option>
                                    <option value="allergies" {{ request('segment') == 'allergies' ? 'selected' : '' }}>Has Allergies</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Sort By</label>
                                <select name="sort" class="form-control">
                                    <option value="last_order" {{ request('sort') == 'last_order' ? 'selected' : '' }}>Last Order</option>
                                    <option value="buyer_name" {{ request('sort') == 'buyer_name' ? 'selected' : '' }}>Name</option>
                                    <option value="orders_count" {{ request('sort') == 'orders_count' ? 'selected' : '' }}>Order Count</option>
                                    <option value="primary_email" {{ request('sort') == 'primary_email' ? 'selected' : '' }}>Email</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Direction</label>
                                <select name="direction" class="form-control">
                                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Customer Statistics --}}
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card bg-light-info">
                    <div class="card-body text-center">
                        <h4>{{ $customers->total() }}</h4>
                        <small>Total Results</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-success">
                    <div class="card-body text-center">
                        <h4>{{ $customers->where('orders_count', '>=', 5)->count() }}</h4>
                        <small>High Value (Current Page)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-warning">
                    <div class="card-body text-center">
                        <h4>{{ $customers->whereNotNull('allergens')->where('allergens', '!=', '')->count() }}</h4>
                        <small>Has Allergies (Current Page)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-danger">
                    <div class="card-body text-center">
                        <h4>{{ $customers->where('marketing_opt_in', true)->count() }}</h4>
                        <small>Marketing Opted In (Current Page)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Table --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Customer List 
                            <small class="text-muted">({{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }})</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th style="min-width: 160px;">Customer</th>
                                        <th style="min-width: 140px;">Contact</th>
                                        <th style="width: 70px;">Orders</th>
                                        <th style="width: 80px;">Occasions</th>
                                        <th style="width: 100px;">Last Order</th>
                                        <th style="max-width: 120px;">Preferences</th>
                                        <th style="width: 80px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong style="font-size: 0.9em;">{{ Str::limit($customer->buyer_name, 20) }}</strong>
                                                @if($customer->orders_count >= 5)
                                                    <i class="fas fa-crown text-warning ms-1" title="High Value Customer"></i>
                                                @endif
                                                @if($customer->marketing_opt_in)
                                                    <i class="fas fa-envelope text-success ms-1" title="Marketing Opt-in"></i>
                                                @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75em;" title="{{ $customer->customer_id }}">ID: {{ Str::limit($customer->customer_id, 15) }}</small>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.85em;" title="{{ $customer->primary_email }}">{{ Str::limit($customer->primary_email, 25) }}</div>
                                            @if($customer->primary_phone)
                                                <small class="text-muted" style="font-size: 0.75em;">{{ $customer->primary_phone }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary badge-pill">{{ $customer->orders_count }}</span>
                                            @if($customer->first_order)
                                                <br><small class="text-muted" style="font-size: 0.7em;">{{ $customer->first_order->format('M Y') }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info badge-pill">{{ $customer->occasions->count() }}</span>
                                            @if($customer->occasions->count() > 0)
                                                <br>
                                                @foreach($customer->occasions->take(2) as $occasion)
                                                    <small class="badge badge-light" style="font-size: 0.7em;">{{ Str::limit(ucfirst($occasion->occasion_type), 8) }}</small>
                                                @endforeach
                                                @if($customer->occasions->count() > 2)
                                                    <small class="text-muted" style="font-size: 0.7em;">+{{ $customer->occasions->count() - 2 }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($customer->last_order)
                                                <div style="font-size: 0.85em;">{{ $customer->last_order->format('M d, Y') }}</div>
                                                <small class="text-muted" style="font-size: 0.7em;">{{ $customer->last_order->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted" style="font-size: 0.85em;">No orders</span>
                                            @endif
                                        </td>
                                        <td style="max-width: 120px;">
                                            @if($customer->allergens)
                                                <span class="badge badge-warning" style="font-size: 0.7em;">Allergies</span>
                                            @endif
                                            @if($customer->eggs_ok === 'No')
                                                <span class="badge badge-danger" style="font-size: 0.7em;">No Eggs</span>
                                            @endif
                                            @if($customer->fav_flavors)
                                                <br><small class="text-muted" style="font-size: 0.7em;" title="{{ $customer->fav_flavors }}">
                                                    {{ Str::limit($customer->fav_flavors, 15) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.crm.customers.show', $customer) }}" 
                                               class="btn btn-xs btn-outline-info" title="View Customer">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <br>No customers found matching your criteria.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($customers->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} 
                                    of {{ $customers->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $customers->appends(request()->query())->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection