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
                        <h3 class="page-title">Weekly Occasions Overview</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}">CRM</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.crm.occasions') }}">Occasions</a></li>
                            <li class="breadcrumb-item active">Weekly View</li>
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
                       class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> List View
                    </a>
                    <a href="{{ route('admin.crm.occasions', ['view' => 'weekly']) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-calendar-week"></i> Weekly View
                    </a>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Weekly Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.crm.occasions', ['view' => 'weekly']) }}">
                    <input type="hidden" name="view" value="weekly">
                    <div class="row">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Show Empty Weeks</label>
                                <select name="show_empty" class="form-control">
                                    <option value="0" {{ request('show_empty') == '0' ? 'selected' : '' }}>Hide Empty</option>
                                    <option value="1" {{ request('show_empty') == '1' ? 'selected' : '' }}>Show Empty</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Weekly Summary Cards --}}
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card bg-light-info">
                    <div class="card-body text-center">
                        <h4>{{ is_countable($weeklyData ?? []) ? count($weeklyData ?? []) : 0 }}</h4>
                        <small>Weeks with Occasions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-success">
                    <div class="card-body text-center">
                        <h4>{{ collect($weeklyData ?? [])->sum('total_occasions') }}</h4>
                        <small>Total Occasions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-warning">
                    <div class="card-body text-center">
                        <h4>{{ collect($weeklyData ?? [])->where('is_reminder_week', true)->count() }}</h4>
                        <small>Reminder Weeks</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-danger">
                    <div class="card-body text-center">
                        <h4>{{ collect($weeklyData ?? [])->sum('high_confidence_count') }}</h4>
                        <small>High Confidence</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weekly Calendar View --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Weekly Calendar</h5>
                    </div>
                    <div class="card-body">
                        @foreach(($weeklyData ?? []) as $week)
                        <div class="card mb-3 {{ $week['is_current_week'] ? 'border-warning' : ($week['is_reminder_week'] ? 'border-info' : '') }}">
                            <div class="card-header {{ $week['is_current_week'] ? 'bg-light-warning' : ($week['is_reminder_week'] ? 'bg-light-info' : '') }}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calendar-week me-2"></i>
                                            {{ $week['week_start']->format('M d') }} - {{ $week['week_end']->format('M d, Y') }}
                                            @if($week['is_current_week'])
                                                <span class="badge badge-warning ms-2">Current Week</span>
                                            @endif
                                            @if($week['is_reminder_week'])
                                                <span class="badge badge-info ms-2">Reminder Week</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $week['week_start']->diffForHumans() }}</small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge badge-primary badge-pill me-2">{{ $week['total_occasions'] }} occasions</span>
                                        <span class="badge badge-success badge-pill">{{ $week['high_confidence_count'] }} high confidence</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($week['occasions']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Occasion</th>
                                                <th>Honoree</th>
                                                <th>Confidence</th>
                                                <th>History</th>
                                                <th>Reminder Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($week['occasions'] as $occasion)
                                            <tr>
                                                <td>
                                                    <strong>{{ $occasion->customer->buyer_name }}</strong>
                                                    @if($occasion->customer->orders_count >= 5)
                                                        <i class="fas fa-crown text-warning ms-1" title="High Value Customer"></i>
                                                    @endif
                                                    <br><small class="text-muted">{{ $occasion->customer->primary_email }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">{{ ucfirst($occasion->occasion_type) }}</span>
                                                </td>
                                                <td>{{ $occasion->honoree_name ?? 'Not specified' }}</td>
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
                                                    <span class="badge badge-info badge-pill">{{ $occasion->history_count }}</span>
                                                    @if($occasion->history_years)
                                                        <br><small class="text-muted">{{ $occasion->history_years }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($occasion->reminder_date)
                                                        {{ $occasion->reminder_date->format('M d') }}
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
                                                    <a href="{{ route('admin.crm.occasions.show', $occasion) }}" 
                                                       class="btn btn-sm btn-outline-info">View</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Occasion Type Breakdown --}}
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <small class="text-muted">
                                            <strong>Types:</strong>
                                            @foreach($week['occasion_types'] as $type => $count)
                                                <span class="badge badge-light me-1">{{ ucfirst($type) }}: {{ $count }}</span>
                                            @endforeach
                                        </small>
                                    </div>
                                </div>
                                @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-calendar fa-2x mb-2"></i>
                                    <br>No occasions scheduled for this week
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        @if(empty($weeklyData ?? []))
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-calendar-times fa-4x mb-3"></i>
                            <br>No occasions found for the selected timeframe.
                            <br><small>Try adjusting your filters or selecting a different timeframe.</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection