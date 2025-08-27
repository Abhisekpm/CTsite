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
                        <h3 class="page-title">CRM Dashboard</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item active">CRM</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- CRM Stats Cards --}}
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Total Customers</h6>
                                <h3>{{ number_format($stats['total_customers']) }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>High Value Customers</h6>
                                <h3>{{ number_format($stats['high_value_customers']) }}</h3>
                                <small class="text-muted">5+ orders</small>
                            </div>
                            <div class="db-icon">
                                <i class="fas fa-crown"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>This Week Occasions</h6>
                                <h3>{{ number_format($stats['this_week_occasions']) }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-comman w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Pending Reminders</h6>
                                <h3>{{ number_format($stats['pending_reminders']) }}</h3>
                            </div>
                            <div class="db-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Occasions Cards --}}
        <div class="row mt-3">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-light-info w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Upcoming Birthdays</h6>
                                <h3>{{ number_format($stats['upcoming_birthdays']) }}</h3>
                                <small class="text-muted">Next 30 days</small>
                            </div>
                            <div class="db-icon">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card bg-light-success w-100">
                    <div class="card-body">
                        <div class="db-widgets d-flex justify-content-between align-items-center">
                            <div class="db-info">
                                <h6>Upcoming Anniversaries</h6>
                                <h3>{{ number_format($stats['upcoming_anniversaries']) }}</h3>
                                <small class="text-muted">Next 30 days</small>
                            </div>
                            <div class="db-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('admin.crm.customers') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-users me-2"></i> View Customers
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.crm.occasions') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-calendar me-2"></i> View Occasions
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <a href="{{ route('admin.crm.occasions', ['view' => 'weekly']) }}" class="btn btn-success btn-block">
                                    <i class="fas fa-calendar-week me-2"></i> Weekly View
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.crm.customers', ['segment' => 'high_value']) }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-crown me-2"></i> VIP Customers
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weekly Occasions Overview --}}
        @if(count($upcomingWeeks) > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Weekly Occasions Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Week</th>
                                        <th>Date Range</th>
                                        <th>Occasions Count</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingWeeks as $week)
                                    <tr class="{{ $week['is_current_week'] ? 'table-warning' : '' }}">
                                        <td>
                                            @if($week['is_current_week'])
                                                <strong>This Week</strong>
                                            @else
                                                Week {{ $loop->iteration }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $week['week_start']->format('M d') }} - {{ $week['week_end']->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <span class="badge badge-primary badge-pill">{{ $week['occasion_count'] }}</span>
                                        </td>
                                        <td>
                                            @if($week['is_current_week'])
                                                <span class="badge badge-warning">Current</span>
                                            @elseif($week['week_start']->isPast())
                                                <span class="badge badge-secondary">Past</span>
                                            @else
                                                <span class="badge badge-info">Upcoming</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.crm.occasions', ['view' => 'weekly', 'timeframe' => 'upcoming']) }}" 
                                               class="btn btn-sm btn-outline-primary">View Details</a>
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

        {{-- Recent Customer Activity --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Customer Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Total Orders</th>
                                        <th>Last Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCustomers as $customer)
                                    <tr>
                                        <td>
                                            <strong>{{ $customer->buyer_name }}</strong>
                                            @if($customer->orders_count >= 5)
                                                <i class="fas fa-crown text-warning ms-1" title="High Value Customer"></i>
                                            @endif
                                        </td>
                                        <td>{{ $customer->primary_email }}</td>
                                        <td>{{ $customer->orders_count }}</td>
                                        <td>{{ $customer->last_order ? $customer->last_order->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.crm.customers.show', $customer) }}" 
                                               class="btn btn-sm btn-outline-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No recent customer activity</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection