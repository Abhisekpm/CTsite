@extends('admin.orders.layout')

@section('title', "Order #{$order->id} Details")

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order #{{ $order->id }} Details</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
    </div>

    <div class="row">
        {{-- Order Details Column --}}
        <div class="col-md-7">
            {{-- Main form wrapping customer and cake details --}}
            <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-4"> {{-- Customer Card --}}
                    <div class="card-header">Customer & Pickup Information</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Customer Name:</dt>
                            <dd class="col-sm-8">
                                <input type="text" class="form-control form-control-sm @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}">
                                @error('customer_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>

                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">{{ $order->email }}</dd>

                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">{{ $order->phone }}</dd>

                            <dt class="col-sm-4">Pickup Date:</dt>
                            <dd class="col-sm-8">
                                <input type="date" class="form-control form-control-sm @error('pickup_date') is-invalid @enderror" id="pickup_date" name="pickup_date" value="{{ old('pickup_date', \Carbon\Carbon::parse($order->pickup_date)->format('Y-m-d')) }}">
                                @error('pickup_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>

                            <dt class="col-sm-4">Pickup Time:</dt>
                            <dd class="col-sm-8">
                                <input type="time" class="form-control form-control-sm @error('pickup_time') is-invalid @enderror" id="pickup_time" name="pickup_time" value="{{ old('pickup_time', \Carbon\Carbon::parse($order->pickup_time)->format('H:i')) }}">
                                @error('pickup_time')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>

                            {{-- <dt class="col-sm-4">Submitted:</dt>
                            <dd class="col-sm-8">{{ $order->created_at->format('M d, Y h:i A') }} ({{ $order->created_at->diffForHumans() }})</dd> --}}

                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8">
                                <span class="badge rounded-pill bg-{{ $order->status == 'pending' ? 'warning text-dark' : ($order->status == 'priced' ? 'info text-dark' : ($order->status == 'confirmed' ? 'success' : 'secondary')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="card mb-4"> {{-- Cake Details Card (NOW INSIDE THE FORM) --}}
                    <div class="card-header">Cake Details</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Size:</dt>
                            <dd class="col-sm-8">
                                <input type="text" class="form-control form-control-sm @error('cake_size') is-invalid @enderror" id="cake_size" name="cake_size" value="{{ old('cake_size', $order->cake_size) }}">
                                @error('cake_size')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>

                            <dt class="col-sm-4">Flavor:</dt>
                            <dd class="col-sm-8">
                                <textarea class="form-control form-control-sm @error('cake_flavor') is-invalid @enderror" id="cake_flavor" name="cake_flavor" rows="2">{{ old('cake_flavor', $order->cake_flavor) }}</textarea>
                                 @error('cake_flavor')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                           </dd>

                            {{-- Added Cake Sponge Field --}}
                            <dt class="col-sm-4">Cake Sponge:</dt>
                            <dd class="col-sm-8">
                                {{-- Changed to select dropdown --}}
                                <select class="form-select form-select-sm @error('cake_sponge') is-invalid @enderror" id="cake_sponge" name="cake_sponge">
                                    <option value="" {{ old('cake_sponge', $order->cake_sponge) == '' ? 'selected' : '' }}>-- Select Sponge --</option>
                                    @php
                                        $spongeOptions = ['chiffon', 'eggless vanilla', 'eggless chocolate', 'mags', 'DTC', 'tres leches'];
                                    @endphp
                                    @foreach ($spongeOptions as $option)
                                        <option value="{{ $option }}" {{ old('cake_sponge', $order->cake_sponge) == $option ? 'selected' : '' }}>
                                            {{ ucfirst($option) }} {{-- Capitalize for display --}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cake_sponge')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>

                            <dt class="col-sm-4">Eggs Ok?</dt>
                            <dd class="col-sm-8">
                                <select class="form-select form-select-sm @error('eggs_ok') is-invalid @enderror" id="eggs_ok" name="eggs_ok">
                                    <option value="Yes" {{ old('eggs_ok', $order->eggs_ok) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ old('eggs_ok', $order->eggs_ok) == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('eggs_ok')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>

                            <dt class="col-sm-4">Allergies:</dt>
                            <dd class="col-sm-8">
                                 {{-- Changed back to input type text --}}
                                 <input type="text" class="form-control form-control-sm @error('allergies') is-invalid @enderror" id="allergies" name="allergies" value="{{ old('allergies', $order->allergies) }}">
                                 @error('allergies')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                           </dd>

                            <dt class="col-sm-4">Message on Cake:</dt>
                            <dd class="col-sm-8">
                                <textarea class="form-control form-control-sm @error('message_on_cake') is-invalid @enderror" id="message_on_cake" name="message_on_cake">{{ old('message_on_cake', $order->message_on_cake) }}</textarea>
                                @error('message_on_cake')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>

                            <dt class="col-sm-4">Custom Decoration:</dt>
                            <dd class="col-sm-8">
                                 <textarea class="form-control form-control-sm @error('custom_decoration') is-invalid @enderror" id="custom_decoration" name="custom_decoration">{{ old('custom_decoration', $order->custom_decoration) }}</textarea>
                                 @error('custom_decoration')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </dd>
                        </dl>

                        {{-- Single Save Button for the customer info + cake details form --}}
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-success">Save Order Details</button>
                        </div>
                    </div> {{-- End Cake Details card-body --}}
                </div> {{-- End Cake Details card --}}

            </form> {{-- End Main Form --}}
        </div>

        {{-- Pricing & Action Column --}}
        <div class="col-md-5">
            {{-- Pricing Form --}}
            <div class="card mb-4">
                <div class="card-header">Set Price</div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.updatePrice', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="input-group mb-3">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" placeholder="Enter price" value="{{ old('price', $order->price) }}" required>
                             @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <p class="small text-muted">Current Status: {{ ucfirst($order->status) }}</p>

                        <button type="submit" class="btn btn-primary w-100">
                            @if ($order->status == 'pending')
                                Set Price & Notify Customer
                            @else
                                Update Price & Re-Notify
                            @endif
                        </button>
                         <p class="small text-muted mt-2">Saving the price will update the status to 'Priced' and trigger an SMS to the customer (if not already confirmed).</p>
                    </form>
                </div>
            </div>

            {{-- Manual Actions Card --}}
            <div class="card mb-4">
                <div class="card-header">Manual Actions</div>
                <div class="card-body text-center"> 
                    {{-- Confirm Button --}}
                    @if($order->status == 'priced')
                        <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="d-inline-block me-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Confirm Order</button>
                        </form>
                    @endif

                    {{-- Cancel Button --}}
                    @if(!in_array($order->status, ['confirmed', 'cancelled']))
                        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</button>
                        </form>
                    @elseif($order->status == 'confirmed')
                        {{-- Pickup Reminder Button for Confirmed Orders --}}
                        <form action="{{ route('admin.orders.sendPickupReminder', $order) }}" method="POST" class="d-inline-block ms-2">
                            @csrf
                            <button type="submit" class="btn btn-info">Send Pickup Reminder</button>
                        </form>
                         <p class="text-muted mt-2 mb-0">Order is already {{ $order->status }}.</p>
                    @else
                         {{-- Show info if already completed/cancelled --}}
                         <p class="text-muted mb-0">Order is already {{ $order->status }}.</p>
                    @endif
                </div>
            </div>

            {{-- Decoration Image --}}
            @if ($order->decoration_image_path)
                <div class="card mb-4">
                    <div class="card-header">Inspiration Photo</div>
                    <div class="card-body text-center">
                        <img src="{{ Storage::url($order->decoration_image_path) }}" alt="Decoration Inspiration" class="img-fluid rounded" style="max-height: 300px;">
                         <a href="{{ Storage::url($order->decoration_image_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">View Full Size</a>
                    </div>
                </div>
            @endif

            {{-- Multiple Decoration Images (new) --}}
            @if ($order->images && $order->images->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">Inspiration Photos ({{ $order->images->count() }})</div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach ($order->images as $image)
                                <div class="col-6">
                                    <div class="position-relative">
                                        <a href="{{ Storage::url($image->path) }}" target="_blank" title="View Full Size">
                                            <img src="{{ Storage::url($image->path) }}" alt="Decoration Inspiration {{ $loop->iteration }}" 
                                                class="img-fluid rounded mb-2" style="width: 100%; height: 150px; object-fit: cover;">
                                        </a>
                                        <div class="position-absolute bottom-0 end-0 m-2">
                                            <a href="{{ Storage::url($image->path) }}" target="_blank" class="btn btn-sm btn-light" title="View Full Size">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Add other actions here? e.g., Mark as Confirmed Manually, Cancel Order --}}
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textareas = [
            document.getElementById('message_on_cake'),
            document.getElementById('custom_decoration')
        ];

        textareas.forEach(textarea => {
            if (textarea) {
                function autoResize() {
                    textarea.style.height = 'auto'; // Reset height to recalculate
                    textarea.style.height = textarea.scrollHeight + 'px';
                }
                textarea.addEventListener('input', autoResize);
                autoResize(); // Initial resize on page load
            }
        });
    });
</script>
@endpush

@endsection 