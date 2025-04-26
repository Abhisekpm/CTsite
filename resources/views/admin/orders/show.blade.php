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
            <div class="card mb-4">
                <div class="card-header">Customer & Pickup Information</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Customer Name:</dt>
                        <dd class="col-sm-8">{{ $order->customer_name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $order->email }}</dd>

                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">{{ $order->phone }}</dd>

                        <dt class="col-sm-4">Pickup Date:</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($order->pickup_date)->format('l, F jS, Y') }}</dd>

                        <dt class="col-sm-4">Pickup Time:</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}</dd>

                        <dt class="col-sm-4">Submitted:</dt>
                        <dd class="col-sm-8">{{ $order->created_at->format('M d, Y h:i A') }} ({{ $order->created_at->diffForHumans() }})</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge rounded-pill bg-{{ $order->status == 'pending' ? 'warning text-dark' : ($order->status == 'priced' ? 'info text-dark' : ($order->status == 'confirmed' ? 'success' : 'secondary')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Cake Details</div>
                <div class="card-body">
                     <dl class="row mb-0">
                        <dt class="col-sm-4">Size:</dt>
                        <dd class="col-sm-8">{{ $order->cake_size }}</dd>

                        <dt class="col-sm-4">Flavor:</dt>
                        <dd class="col-sm-8">{{ $order->cake_flavor }}</dd>

                        <dt class="col-sm-4">Eggs Ok?</dt>
                        <dd class="col-sm-8">{{ $order->eggs_ok }}</dd>

                        <dt class="col-sm-4">Message on Cake:</dt>
                        <dd class="col-sm-8">{{ $order->message_on_cake ?: '-' }}</dd>

                        <dt class="col-sm-4">Allergies:</dt>
                        <dd class="col-sm-8">{{ $order->allergies ?: '-' }}</dd>

                        <dt class="col-sm-4">Custom Decoration:</dt>
                        <dd class="col-sm-8">{{ $order->custom_decoration ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
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
@endsection 