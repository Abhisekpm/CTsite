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
                        <h3 class="page-title">CRM Occasions</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}">CRM</a></li>
                            <li class="breadcrumb-item active">Occasions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- View Toggle --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.crm.occasions') }}" 
                       class="btn {{ !request('view') || request('view') == 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-list"></i> List View
                    </a>
                    <a href="{{ route('admin.crm.occasions', ['view' => 'weekly']) }}" 
                       class="btn {{ request('view') == 'weekly' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-week"></i> Weekly View
                    </a>
                </div>
            </div>
        </div>

        {{-- Filters and Search --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Occasion Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.crm.occasions') }}">
                    <input type="hidden" name="view" value="{{ request('view', 'list') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Customer name or honoree">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Occasion Type</label>
                                <select name="occasion_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="birthday" {{ request('occasion_type') == 'birthday' ? 'selected' : '' }}>Birthday</option>
                                    <option value="anniversary" {{ request('occasion_type') == 'anniversary' ? 'selected' : '' }}>Anniversary</option>
                                    <option value="graduation" {{ request('occasion_type') == 'graduation' ? 'selected' : '' }}>Graduation</option>
                                    <option value="baby_shower" {{ request('occasion_type') == 'baby_shower' ? 'selected' : '' }}>Baby Shower</option>
                                    <option value="gender_reveal" {{ request('occasion_type') == 'gender_reveal' ? 'selected' : '' }}>Gender Reveal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Timeframe</label>
                                <select name="timeframe" class="form-control">
                                    <option value="upcoming" {{ request('timeframe', 'upcoming') == 'upcoming' ? 'selected' : '' }}>Next 6 Months</option>
                                    <option value="current_month" {{ request('timeframe') == 'current_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="next_month" {{ request('timeframe') == 'next_month' ? 'selected' : '' }}>Next Month</option>
                                    <option value="next_3_months" {{ request('timeframe') == 'next_3_months' ? 'selected' : '' }}>Next 3 Months</option>
                                    <option value="all" {{ request('timeframe') == 'all' ? 'selected' : '' }}>All Future Occasions</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Show</label>
                                <select name="order_filter" class="form-control">
                                    <option value="">All Occasions</option>
                                    <option value="recent_orders" {{ request('order_filter') == 'recent_orders' ? 'selected' : '' }}>Recent Orders</option>
                                    <option value="no_orders" {{ request('order_filter') == 'no_orders' ? 'selected' : '' }}>No Previous Orders</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Sort By</label>
                                <select name="sort" class="form-control">
                                    <option value="reminder_date" {{ request('sort') == 'reminder_date' ? 'selected' : '' }}>Reminder Date</option>
                                    <option value="anchor_week" {{ request('sort') == 'anchor_week' ? 'selected' : '' }}>Anchor Week</option>
                                    <option value="last_order_date" {{ request('sort') == 'last_order_date' ? 'selected' : '' }}>Last Order Date</option>
                                    <option value="customer_name" {{ request('sort') == 'customer_name' ? 'selected' : '' }}>Customer Name</option>
                                    <option value="occasion_type" {{ request('sort') == 'occasion_type' ? 'selected' : '' }}>Occasion Type</option>
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

        {{-- Occasion Statistics --}}
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card bg-light-info">
                    <div class="card-body text-center">
                        <h4>{{ $occasions->total() }}</h4>
                        <small>Total Results</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-success">
                    <div class="card-body text-center">
                        <h4>{{ $occasions->whereNotNull('last_order_date_latest')->count() }}</h4>
                        <small>With Order History (Current Page)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-warning">
                    <div class="card-body text-center">
                        <h4>{{ $occasions->where('reminder_date', '<=', now()->addDays(7))->count() }}</h4>
                        <small>Due This Week (Current Page)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-danger">
                    <div class="card-body text-center">
                        <h4>{{ $occasions->where('reminder_sent', false)->where('reminder_date', '<=', now())->count() }}</h4>
                        <small>Overdue Reminders (Current Page)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Occasions Table --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            Occasions List 
                            <small class="text-muted">({{ $occasions->firstItem() ?? 0 }} - {{ $occasions->lastItem() ?? 0 }} of {{ $occasions->total() }})</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th style="min-width: 150px;">Customer</th>
                                        <th style="width: 90px;">Occasion</th>
                                        <th style="width: 110px;">Anchor Week</th>
                                        <th style="width: 120px;">Next Occurence</th>
                                        <th style="width: 100px;">Reminder</th>
                                        <th style="width: 80px;">Last Ordered</th>
                                        <th style="width: 70px;">History</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($occasions as $occasion)
                                    <tr class="{{ $occasion->reminder_date && $occasion->reminder_date->isPast() && !$occasion->reminder_sent ? 'table-warning' : '' }}">
                                        <td>
                                            <div>
                                                <strong style="font-size: 0.9em;">{{ Str::limit($occasion->customer->buyer_name, 20) }}</strong>
                                                @if($occasion->customer->orders_count >= 5)
                                                    <i class="fas fa-crown text-warning ms-1" title="High Value Customer"></i>
                                                @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75em;" title="{{ $occasion->customer->primary_email }}">{{ Str::limit($occasion->customer->primary_email, 20) }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div>
                                                <span class="badge badge-primary" style="font-size: 0.7em;">{{ ucfirst($occasion->occasion_type) }}</span>
                                            </div>
                                            @if($occasion->honoree_name)
                                                <small class="text-muted" style="font-size: 0.7em;" title="{{ $occasion->honoree_name }}">{{ Str::limit($occasion->honoree_name, 12) }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div style="font-size: 0.85em;">{{ $occasion->anchor_week_start_date->format('M d') }} - {{ $occasion->anchor_week_start_date->endOfWeek()->format('M d') }}</div>
                                            <small class="text-muted" style="font-size: 0.7em;">{{ $occasion->anchor_week_start_date->format('Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if($occasion->next_anticipated_order_date)
                                                <div style="font-size: 0.85em;">{{ $occasion->next_anticipated_order_date->format('M d, Y') }}</div>
                                                <small class="text-muted" style="font-size: 0.7em;">{{ $occasion->next_anticipated_order_date->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted" style="font-size: 0.85em;">Not calculated</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($occasion->reminder_date)
                                                <div class="{{ $occasion->reminder_date->isPast() && !$occasion->reminder_sent ? 'text-danger' : '' }}" style="font-size: 0.85em;">
                                                    {{ $occasion->reminder_date->format('M d, Y') }}
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7em;">{{ $occasion->reminder_date->diffForHumans() }}</small>
                                                @if($occasion->reminder_sent)
                                                    <br><span class="badge badge-success badge-sm" style="font-size: 0.6em;">Sent</span>
                                                @elseif($occasion->reminder_date->isPast())
                                                    <br><span class="badge badge-danger badge-sm" style="font-size: 0.6em;">Overdue</span>
                                                @endif
                                            @else
                                                <span class="text-muted" style="font-size: 0.85em;">Not set</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($occasion->last_order_date_latest)
                                                <div style="font-size: 0.8em;">{{ \Carbon\Carbon::parse($occasion->last_order_date_latest)->format('M d, Y') }}</div>
                                                <small class="text-muted" style="font-size: 0.6em;">{{ \Carbon\Carbon::parse($occasion->last_order_date_latest)->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted" style="font-size: 0.7em;">No orders</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info badge-pill" style="font-size: 0.7em;">{{ $occasion->history_count }}</span>
                                            @if($occasion->history_years)
                                                <br><small class="text-muted" style="font-size: 0.6em;">{{ Str::limit($occasion->history_years, 8) }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.crm.occasions.show', $occasion) }}" 
                                               class="btn btn-xs btn-outline-info me-1" title="View Occasion">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.crm.customers.show', $occasion->customer) }}" 
                                               class="btn btn-xs btn-outline-secondary" title="View Customer">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar fa-3x mb-3"></i>
                                            <br>No occasions found matching your criteria.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($occasions->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $occasions->firstItem() }} to {{ $occasions->lastItem() }} 
                                    of {{ $occasions->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $occasions->appends(request()->query())->links() }}
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