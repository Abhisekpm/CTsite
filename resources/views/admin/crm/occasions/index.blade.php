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
                                <label>Confidence</label>
                                <select name="confidence" class="form-control">
                                    <option value="">All Confidence</option>
                                    <option value="high" {{ request('confidence') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ request('confidence') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ request('confidence') == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Sort By</label>
                                <select name="sort" class="form-control">
                                    <option value="reminder_date" {{ request('sort') == 'reminder_date' ? 'selected' : '' }}>Reminder Date</option>
                                    <option value="anchor_week" {{ request('sort') == 'anchor_week' ? 'selected' : '' }}>Anchor Week</option>
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
                        <h4>{{ $occasions->where('anchor_confidence', 'high')->count() }}</h4>
                        <small>High Confidence (Current Page)</small>
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
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Occasion</th>
                                        <th>Anchor Week</th>
                                        <th>Next Anticipated Order</th>
                                        <th>Reminder</th>
                                        <th>Confidence</th>
                                        <th>History</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($occasions as $occasion)
                                    <tr class="{{ $occasion->reminder_date && $occasion->reminder_date->isPast() && !$occasion->reminder_sent ? 'table-warning' : '' }}">
                                        <td>
                                            <div>
                                                <strong>{{ $occasion->customer->buyer_name }}</strong>
                                                @if($occasion->customer->orders_count >= 5)
                                                    <i class="fas fa-crown text-warning ms-1" title="High Value Customer"></i>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $occasion->customer->primary_email }}</small>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="badge badge-primary">{{ ucfirst($occasion->occasion_type) }}</span>
                                            </div>
                                            @if($occasion->honoree_name)
                                                <small class="text-muted">{{ $occasion->honoree_name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $occasion->anchor_week_start_date->format('M d') }} - {{ $occasion->anchor_week_start_date->endOfWeek()->format('M d') }}</div>
                                            <small class="text-muted">{{ $occasion->anchor_week_start_date->format('Y') }}</small>
                                        </td>
                                        <td>
                                            @if($occasion->next_anticipated_order_date)
                                                <div>{{ $occasion->next_anticipated_order_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $occasion->next_anticipated_order_date->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">Not calculated</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($occasion->reminder_date)
                                                <div class="{{ $occasion->reminder_date->isPast() && !$occasion->reminder_sent ? 'text-danger' : '' }}">
                                                    {{ $occasion->reminder_date->format('M d, Y') }}
                                                </div>
                                                <small class="text-muted">{{ $occasion->reminder_date->diffForHumans() }}</small>
                                                @if($occasion->reminder_sent)
                                                    <br><span class="badge badge-success badge-sm">Sent</span>
                                                @elseif($occasion->reminder_date->isPast())
                                                    <br><span class="badge badge-danger badge-sm">Overdue</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
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
                                            <span class="badge badge-info badge-pill">{{ $occasion->history_count }}</span> orders
                                            @if($occasion->history_years)
                                                <br><small class="text-muted">{{ $occasion->history_years }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.crm.occasions.show', $occasion) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.crm.customers.show', $occasion->customer) }}" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-user"></i> Customer
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