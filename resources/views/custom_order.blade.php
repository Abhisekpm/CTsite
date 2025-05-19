@extends('frontend.layout.main')

@section('title', 'Custom Cake Order - ' . $settings->website_name ?? 'Chocolate Therapy')
{{-- Add meta keywords/description if desired --}}
@section('meta_keywords', '')
@section('meta_description', '')

@section('main')
    {{-- Hero Header --}}
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center my-5 pt-5 pb-4">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Custom Cake Order</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Custom Order</li>
                </ol>
            </nav>
        </div>
    </div>
    {{-- Hero Header End --}}

    {{-- Order Form Section --}}
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-8 offset-lg-2 wow fadeInUp" data-wow-delay="0.2s">
                    <h5 class="section-title ff-secondary text-start text-primary fw-normal">Place Your Order</h5>
                    <h1 class="mb-4">Request a Custom Cake</h1>

                    {{-- Display Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Display Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">Please fix the following errors:</h6>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('custom-order.store') }}" method="POST" id="customOrderForm" enctype="multipart/form-data">
                        @csrf

                        {{-- Tab Navigation --}}
                        <ul class="nav nav-tabs mb-3" id="orderTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-pane" type="button" role="tab" aria-controls="contact-pane" aria-selected="true">1. Contact Info</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="size-tab" data-bs-toggle="tab" data-bs-target="#size-pane" type="button" role="tab" aria-controls="size-pane" aria-selected="false">2. Cake Size</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="flavor-tab" data-bs-toggle="tab" data-bs-target="#flavor-pane" type="button" role="tab" aria-controls="flavor-pane" aria-selected="false">3. Cake Flavor</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="decoration-tab" data-bs-toggle="tab" data-bs-target="#decoration-pane" type="button" role="tab" aria-controls="decoration-pane" aria-selected="false">4. Cake Decoration</button>
                            </li>
                        </ul>

                        {{-- Tab Content --}}
                        <div class="tab-content" id="orderTabsContent">

                            {{-- Tab 1: Contact Info --}}
                            <div class="tab-pane fade show active" id="contact-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                                <h5 class="mb-3">Step 1: Contact, Pickup & Dietary Info</h5>
                                <div class="row g-3">
                                    {{-- Name --}}
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" placeholder="Your Name" value="{{ old('customer_name') }}" required>
                                            <label for="customer_name">Your Name</label>
                                            @error('customer_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <div id="customer_name_js_error" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Your Email" value="{{ old('email') }}" required>
                                            <label for="email">Your Email</label>
                                            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <div id="email_js_error" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    {{-- Phone --}}
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Your Phone Number" value="{{ old('phone') }}" required>
                                            <label for="phone">Phone Number</label>
                                            @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <div id="phone_js_error" class="invalid-feedback"></div>
                                            {{-- Add Opt-in Text --}}
                                            <small class="form-text text-muted">
                                                (By providing your phone number, you agree to receive text message notifications about your order status)
                                            </small>
                                        </div>
                                    </div>
                                    {{-- Pickup Date --}}
                                    <div class="col-md-6">
                                        {{-- Wrapper for Tempus Dominus --}}
                                        <div class="input-group" id="pickup_date_picker" data-td-target-input="nearest" data-td-target-toggle="nearest">
                                            <input type="text" {{-- Changed type to text --}}
                                                class="form-control @error('pickup_date') is-invalid @enderror" 
                                                id="pickup_date" 
                                                name="pickup_date" 
                                                placeholder="Select Pickup Date" 
                                                value="{{ old('pickup_date') }}" 
                                                data-td-target="#pickup_date_picker" 
                                                required />
                                            <span class="input-group-text" data-td-target="#pickup_date_picker" data-td-toggle="datetimepicker">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            @error('pickup_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                        <div id="pickup_date_js_error" class="text-danger small mt-1"></div>
                                    </div>
                                    {{-- Pickup Time --}}
                                    <div class="col-md-6">
                                        {{-- Wrapper for Tempus Dominus --}}
                                        <div class="input-group">
                                            <input type="time"
                                                class="form-control @error('pickup_time') is-invalid @enderror" 
                                                id="pickup_time" 
                                                name="pickup_time" 
                                                placeholder="Select Pickup Time (11am-7pm)" 
                                                value="{{ old('pickup_time') }}"
                                                min="11:00"
                                                max="19:00"
                                                step="900"
                                                required>
                                            <span class="input-group-text">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            @error('pickup_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                        <div id="pickup_time_js_error" class="text-danger small mt-1"></div>
                                    </div>
                                    {{-- Eggs Ok? --}}
                                    <div class="col-md-6">
                                        <label class="form-label mb-0">Eggs Ok?</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="eggs_ok" id="eggs_yes" value="Yes" {{ old('eggs_ok', 'Yes') == 'Yes' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="eggs_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="eggs_ok" id="eggs_no" value="No" {{ old('eggs_ok') == 'No' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="eggs_no">No (Request Eggless)</label>
                                        </div>
                                    </div>
                                     {{-- Allergies --}}
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('allergies') is-invalid @enderror" placeholder="Please list any allergies" id="allergies" name="allergies" style="height: 80px">{{ old('allergies') }}</textarea>
                                            <label for="allergies">Any Allergies?</label>
                                            @error('allergies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- Navigation --}}
                                    <div class="col-12 text-end mt-3">
                                        <button class="btn btn-primary py-2 px-4 btn-next" type="button">Next <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Cake Size --}}
                            <div class="tab-pane fade" id="size-pane" role="tabpanel" aria-labelledby="size-tab" tabindex="0">
                                <h5 class="mb-3">Step 2: Select Cake Size</h5>
                                <div class="row g-3">
                                    {{-- Cake Size Dropdown --}}
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <select class="form-select @error('cake_size') is-invalid @enderror" id="cake_size" name="cake_size" required>
                                                <option value="" {{ old('cake_size') == '' ? 'selected' : '' }} disabled>Select Size...</option>
                                                <optgroup label="Single Tier">
                                                    <option value='6"' {{ old('cake_size') == '6"' ? 'selected' : '' }}>6" Round</option>
                                                    <option value='8"' {{ old('cake_size') == '8"' ? 'selected' : '' }}>8" Round</option>
                                                    <option value='9"' {{ old('cake_size') == '9"' ? 'selected' : '' }}>9" Round</option>
                                                    <option value='10"' {{ old('cake_size') == '10"' ? 'selected' : '' }}>10" Round</option>
                                                </optgroup>
                                                <optgroup label="Double Tier">
                                                    <option value='6" on 8"' {{ old('cake_size') == '6" on 8"' ? 'selected' : '' }}>6" on 8"</option>
                                                    <option value='6" on 9"' {{ old('cake_size') == '6" on 9"' ? 'selected' : '' }}>6" on 9"</option>
                                                    <option value='6" on 10"' {{ old('cake_size') == '6" on 10"' ? 'selected' : '' }}>6" on 10"</option>
                                                </optgroup>
                                                 <option value="Other" {{ old('cake_size') == 'Other' ? 'selected' : '' }}>Other (Describe in Notes)</option>
                                            </select>
                                            <label for="cake_size">Cake Size Selection</label>
                                            @error('cake_size') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <div id="cake_size_js_error" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    {{-- Navigation Moved Up --}}
                                    <div class="col-6 text-start mt-3"> {{-- Added mt-3 for spacing --}}
                                        <button class="btn btn-secondary py-2 px-4 btn-prev" type="button"><i class="fas fa-arrow-left me-1"></i> Previous</button>
                                    </div>
                                    <div class="col-6 text-end mt-3"> {{-- Added mt-3 for spacing --}}
                                        <button class="btn btn-primary py-2 px-4 btn-next" type="button">Next <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>

                                    {{-- Size/Price Visual Guide --}}
                                    <div class="col-12 mt-4 mb-3">
                                        <h6>Size & Base Price Guide:</h6>
                                        <div class="row">
                                            {{-- Single Tier Column --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-secondary text-white">Single Tier Cakes</div>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><i class="fas fa-square text-primary me-2"></i>6 Inch <small class="text-muted">(Serves 10)</small></span>
                                                            <span class="badge bg-light text-dark">$50+</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><i class="fas fa-square text-primary me-2"></i>8 Inch <small class="text-muted">(Serves 15)</small></span>
                                                            <span class="badge bg-light text-dark">$70+</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><i class="fas fa-square text-primary me-2"></i>9 Inch <small class="text-muted">(Serves 25)</small></span>
                                                            <span class="badge bg-light text-dark">$90+</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><i class="fas fa-square text-primary me-2"></i>10 Inch <small class="text-muted">(Serves 35)</small></span>
                                                            <span class="badge bg-light text-dark">$120+</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            {{-- Double Tier Column --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100">
                                                     <div class="card-header bg-info text-white">Double Tier Cakes</div>
                                                     <ul class="list-group list-group-flush">
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><i class="fas fa-square text-info me-2"></i>6" on 8" <small class="text-muted">(Serves 30-35)</small></span>
                                                            <span class="badge bg-light text-dark">Custom Price</span>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><i class="fas fa-square text-info me-2"></i>6" on 9" <small class="text-muted">(Serves 40-45)</small></span>
                                                            <span class="badge bg-light text-dark">Custom Price</span>
                                                        </li>
                                                         <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span><i class="fas fa-square text-info me-2"></i>6" on 10" <small class="text-muted">(Serves 50-55)</small></span>
                                                            <span class="badge bg-light text-dark">Custom Price</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="d-block text-center">(Prices are base estimates, final price depends on flavor and decoration.)</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 3: Cake Flavor --}}
                            <div class="tab-pane fade" id="flavor-pane" role="tabpanel" aria-labelledby="flavor-tab" tabindex="0">
                                 <h5 class="mb-3">Step 3: Select Flavor</h5>
                                 <p class="text-muted">Click on desired cake flavor from list below</p>
                                <div class="row g-3">
                                    {{-- Cake Flavor Input --}}
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('cake_flavor') is-invalid @enderror" id="cake_flavor" name="cake_flavor" placeholder="Cake Flavor" value="{{ old('cake_flavor') }}" required>
                                            <label for="cake_flavor"></label>
                                            @error('cake_flavor') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <div id="cake_flavor_js_error" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                     {{-- Navigation Moved Up --}}
                                    <div class="col-6 text-start mt-3"> {{-- Added mt-3 for spacing --}}
                                         <button class="btn btn-secondary py-2 px-4 btn-prev" type="button"><i class="fas fa-arrow-left me-1"></i> Previous</button>
                                     </div>
                                    <div class="col-6 text-end mt-3"> {{-- Added mt-3 for spacing --}}
                                        <button class="btn btn-primary py-2 px-4 btn-next" type="button">Next <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>

                                    {{-- Flavor Profiles (Static 3-Column Layout) --}}
                                    <div class="col-12 mt-4 mb-3"> {{-- Changed mt-3 to mt-4 --}}
                                        <h6>Flavor Profiles Guide:</h6>
                                        <div class="row">
                                            {{-- Column 1: Vanilla Base --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light">Vanilla Base</div>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item clickable-flavor" data-flavor="Fresh Fruit Cake (Specify fruit)" style="cursor: pointer;">
                                                            <h6 class="mb-0">Fresh Fruit Cakes</h6>
                                                            <small class="text-muted d-block">(choose from either litchi,mango, strawberry, pineapple)</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Caramel Mousse with Strawberry" style="cursor: pointer;">
                                                            <h6 class="mb-0">Caramel Mousse</h6>
                                                            <small class="text-muted d-block">with Strawberry</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="White Chocolate Mousse with Raspberry Compote" style="cursor: pointer;">
                                                            <h6 class="mb-0">White Chocolate Mousse</h6>
                                                            <small class="text-muted d-block">with Raspberry Compote</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Coconut Mousse with Fresh Mango" style="cursor: pointer;">
                                                            <h6 class="mb-0">Coconut Mousse</h6>
                                                            <small class="text-muted d-block">with Fresh Mango</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Butterscotch (Caramel and Nougat)" style="cursor: pointer;">
                                                            <h6 class="mb-0">Butterscotch</h6>
                                                            <small class="text-muted d-block">(Caramel and Nougat)</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Victoria (Raspberry and Strawberry Compote)" style="cursor: pointer;">
                                                            <h6 class="mb-0">Victoria</h6>
                                                            <small class="text-muted d-block">(Raspberry and Strawberry Compote)</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Lemon Pineapple" style="cursor: pointer;">
                                                            <h6 class="mb-0">Lemon Pineapple</h6>
                                                            <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Pinacolada" style="cursor: pointer;">
                                                            <h6 class="mb-0">Pinacolada</h6>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Tiramisu" style="cursor: pointer;">
                                                            <h6 class="mb-0">Tiramisu</h6>
                                                            <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Russian Honey" style="cursor: pointer;">
                                                            <h6 class="mb-0">Russian Honey</h6>
                                                             <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Fig & Honey" style="cursor: pointer;">
                                                            <h6 class="mb-0">Fig & Honey</h6>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Custard Orange Nougat" style="cursor: pointer;">
                                                            <h6 class="mb-0">Custard Orange Nougat</h6>
                                                            <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Banana Cake (Specify Mousse)" style="cursor: pointer;">
                                                            <h6 class="mb-0">Banana Cake</h6>
                                                            <small class="text-muted d-block">(with Nutella or Peanut Butter Mousse)</small>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            {{-- Column 2: Chocolate Base --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-secondary text-white">Chocolate Base</div>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item clickable-flavor" data-flavor="Chocolate Cake soaked in Coffee (Specify Mousse)" style="cursor: pointer;">
                                                            <h6 class="mb-0">Chocolate Cake soaked in Coffee</h6>
                                                            <small class="text-muted d-block">with Chocolate or Nutella Mousse</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Chocolate Cake with Chocolate Orange Nougat" style="cursor: pointer;">
                                                            <h6 class="mb-0">Chocolate Cake</h6>
                                                            <small class="text-muted d-block">with Chocolate Orange Nougat</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="White Chocolate Mousse with Strawberries" style="cursor: pointer;">
                                                            <h6 class="mb-0">White Chocolate Mousse</h6>
                                                            <small class="text-muted d-block">with Strawberries</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Cold Coffee" style="cursor: pointer;">
                                                            <h6 class="mb-0">Cold Coffee</h6>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Chocolate and Mint Mousse" style="cursor: pointer;">
                                                            <h6 class="mb-0">Chocolate and Mint Mousse</h6>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Cookies and Cream (specify Mousse or Buttercream)" style="cursor: pointer;">
                                                            <h6 class="mb-0">Cookies and Cream Mousse or Buttercream</h6>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Peanut (specify Mousse or Buttercream)" style="cursor: pointer;">
                                                            <h6 class="mb-0">Peanut Mousse or Buttercream</h6>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Black Forest" style="cursor: pointer;">
                                                            <h6 class="mb-0">Black Forest</h6>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="German Chocolate Cake" style="cursor: pointer;">
                                                            <h6 class="mb-0">German Chocolate Cake</h6>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Hot Chocolate Cake with Marshmallows" style="cursor: pointer;">
                                                            <h6 class="mb-0">Hot Chocolate Cake</h6>
                                                            <small class="text-muted d-block">with Marshmallows</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Millionaire Cake" style="cursor: pointer;">
                                                            <h6 class="mb-0">Millionaire cake</h6>
                                                            <small class="text-muted d-block">(Chocolate Buttercream, Caramel and Crispy Cookie Crunch)</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Hazelnut Concord" style="cursor: pointer;">
                                                            <h6 class="mb-0">Hazelnut Concord</h6>
                                                            <small class="text-muted d-block">(Chocolate Mousse with Crispy Hazelnut Meringue)</small>
                                                            <small class="text-danger d-block">Cannot be custom decorated, Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Sacher Torte" style="cursor: pointer;">
                                                            <h6 class="mb-0">Sacher Torte</h6>
                                                            <small class="text-muted d-block">(Apricot Jam and Chocolate Glaze)</small>
                                                             <small class="text-danger d-block">Cannot be custom decorated, Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Chocolate Opera" style="cursor: pointer;">
                                                            <h6 class="mb-0">Chocolate Opera</h6>
                                                            <small class="text-muted d-block">(Almond Cake with Coffee Buttercream and Ganache)</small>
                                                            <small class="text-danger d-block">Cannot be custom decorated, Cannot be Eggless</small>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            {{-- Column 3: Indian Fusion Flavours --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-info text-white">Indian Fusion Flavours</div>
                                                    <ul class="list-group list-group-flush">
                                                         <li class="list-group-item clickable-flavor" data-flavor="Rasmalai Tres Leches" style="cursor: pointer;">
                                                            <h6 class="mb-0">Rasmalai Tres Leches</h6>
                                                            <small class="text-danger d-block">Cannot be Custom Decorated, Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Rose Tres Leches" style="cursor: pointer;">
                                                            <h6 class="mb-0">Rose Tres Leches</h6>
                                                             <small class="text-danger d-block">Cannot be Custom Decorated, Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Litchi Tres Leches" style="cursor: pointer;">
                                                            <h6 class="mb-0">Litchi Tres Leches</h6>
                                                            <small class="text-danger d-block">Cannot be Custom Decorated, Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Gulab Jamun" style="cursor: pointer;">
                                                            <h6 class="mb-0">Gulab Jamun</h6>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Gajar Halwa" style="cursor: pointer;">
                                                            <h6 class="mb-0">Gajar Halwa</h6>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Paan" style="cursor: pointer;">
                                                            <h6 class="mb-0">Paan</h6>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Thandai" style="cursor: pointer;">
                                                            <h6 class="mb-0">Thandai</h6>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Rabdi" style="cursor: pointer;">
                                                            <h6 class="mb-0">Rabdi</h6>
                                                        </li>
                                                         <li class="list-group-item clickable-flavor" data-flavor="Mango Rabdi" style="cursor: pointer;">
                                                            <h6 class="mb-0">Mango Rabdi</h6>
                                                        </li>
                                                        <li class="list-group-item clickable-flavor" data-flavor="Sitaphal" style="cursor: pointer;">
                                                            <h6 class="mb-0">Sitaphal</h6>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 4: Cake Decoration --}}
                            <div class="tab-pane fade" id="decoration-pane" role="tabpanel" aria-labelledby="decoration-tab" tabindex="0">
                                 <h5 class="mb-3">Step 4: Decoration & Message</h5>
                                <div class="row g-3">
                                     {{-- Message on Cake --}}
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('message_on_cake') is-invalid @enderror" placeholder="Any specific message to write on the cake? (e.g., Happy Birthday Sarah)" id="message_on_cake" name="message_on_cake" style="height: 80px">{{ old('message_on_cake') }}</textarea>
                                            <label for="message_on_cake">Message on Cake (Optional)</label>
                                            @error('message_on_cake') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- Custom Decoration Details --}}
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('custom_decoration') is-invalid @enderror" placeholder="Describe your custom decoration ideas (colors, theme, style, etc.)" id="custom_decoration" name="custom_decoration" style="height: 100px">{{ old('custom_decoration') }}</textarea>
                                            <label for="custom_decoration">Custom Decoration Ideas</label>
                                            @error('custom_decoration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    {{-- Image Upload --}}
                                    <div class="col-12">
                                        <label for="decoration_images" class="form-label">Upload Image(s) for Inspiration (Optional):</label>
                                        <input class="form-control @error('decoration_images') is-invalid @enderror" type="file" id="decoration_images" name="decoration_images[]" multiple>
                                        <small class="form-text text-muted">You can upload multiple images (JPG, PNG, GIF). Max 5MB per image.</small>
                                        @error('decoration_images') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        @error('decoration_images.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror {{-- Error for individual files in array --}}
                                        
                                        {{-- Image Preview Container --}}
                                        <div id="image-preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>
                                    </div>

                                    {{-- Navigation --}}
                                     <div class="col-6 text-start mt-3">
                                         <button class="btn btn-secondary py-2 px-4 btn-prev" type="button"><i class="fas fa-arrow-left me-1"></i> Previous</button>
                                     </div>
                                    <div class="col-6 text-end mt-3">
                                        <button class="btn btn-primary w-100 py-3" type="submit" id="submitOrderBtn">Submit Order Request</button>
                                    </div>
                                </div>
                            </div>

                        </div> {{-- End Tab Content --}}
                    </form>

                    </div>
            </div>
        </div>
    </div>
    {{-- Order Form Section End --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('customOrderForm');
    const tabTriggers = form.querySelectorAll('#orderTabs button[data-bs-toggle="tab"]');
    const tabs = Array.from(tabTriggers).map(trigger => new bootstrap.Tab(trigger));
    const tabPanes = form.querySelectorAll('.tab-content .tab-pane');
    const nextButtons = form.querySelectorAll('.btn-next');
    const prevButtons = form.querySelectorAll('.btn-prev');

    // --- Validation Helper Functions ---
    function showError(inputId, message) {
        const inputElement = document.getElementById(inputId);
        const errorDiv = document.getElementById(inputId + '_js_error');
        if (inputElement) inputElement.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block'; // Ensure it shows
        }
    }

    function clearError(inputId) {
        const inputElement = document.getElementById(inputId);
        const errorDiv = document.getElementById(inputId + '_js_error');
        if (inputElement) inputElement.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none'; // Hide it
        }
        // Also clear specific date/time errors
        if (inputId === 'pickup_date' || inputId === 'pickup_time') {
            const specificErrorDiv = document.getElementById(inputId + '_js_error');
            if (specificErrorDiv) specificErrorDiv.textContent = '';
        }
    }

    // --- Specific Field Validation Functions ---
    function validateRequired(inputElement) {
        clearError(inputElement.id);
        if (!inputElement.value.trim()) {
            showError(inputElement.id, 'This field is required.');
            return false;
        }
        return true;
    }

    function validateEmail(inputElement) {
        clearError(inputElement.id);
        if (!inputElement.value.trim()) {
            showError(inputElement.id, 'This field is required.');
            return false;
        } 
        // Basic email pattern check
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(inputElement.value)) {
             showError(inputElement.id, 'Please enter a valid email address.');
             return false;
        }
        return true;
    }

     function validateSelect(selectElement) {
        clearError(selectElement.id);
        if (!selectElement.value) {
             showError(selectElement.id, 'Please make a selection.');
             return false;
        }
        return true;
    }
    
    // --- Initialize Tempus Dominus --- 
    const pickupDatePickerEl = document.getElementById('pickup_date_picker');
    if (pickupDatePickerEl) {
        const threeDaysFromNow = new Date();
        threeDaysFromNow.setDate(threeDaysFromNow.getDate() + 3);

        new tempusDominus.TempusDominus(pickupDatePickerEl, {
            localization: {
                format: 'yyyy-MM-dd', 
                locale: 'en-US'
            },
            display: {
                icons: { time: 'fas fa-clock', date: 'fas fa-calendar', up: 'fas fa-chevron-up', down: 'fas fa-chevron-down', previous: 'fas fa-chevron-left', next: 'fas fa-chevron-right', today: 'fas fa-calendar-check', clear: 'fas fa-trash', close: 'fas fa-times' },
                buttons: { today: true, clear: false, close: true },
                components: { calendar: true, date: true, month: true, year: true, decades: true, clock: false, hours: false, minutes: false, seconds: false }
            },
            restrictions: {
                minDate: threeDaysFromNow, 
                daysOfWeekDisabled: [1] // Disable Mondays
            }
        });
    }

    // --- Tab Validation Function ---
    function validateTab(tabIndex) {
        let isTabValid = true;
        let firstInvalidElement = null;
        const currentPane = tabPanes[tabIndex];
        const fieldsToValidate = currentPane.querySelectorAll('input[required], select[required], textarea[required]');

        fieldsToValidate.forEach(field => {
            let fieldValid = true;
            if (field.tagName === 'SELECT') {
                fieldValid = validateSelect(field);
            } else if (field.type === 'email') {
                fieldValid = validateEmail(field);
            } else if (field.id === 'pickup_date') { // Check by ID for date picker
                 fieldValid = validateRequired(field); // Basic required check
            } else if (field.id === 'pickup_time') { // Check by ID for time picker
                 fieldValid = validateRequired(field); 
                 if (fieldValid) { // Only check time if a value exists
                    const timeValue = field.value; // Value is in HH:mm format
                    const minTime = "11:00";
                    const maxTime = "19:00";
                    if (timeValue < minTime || timeValue > maxTime) {
                        showError(field.id, 'Pickup time must be between 11:00 AM and 7:00 PM.');
                        fieldValid = false;
                    }
                 }
            } else { // Includes text, tel, textarea etc. marked as required
                 fieldValid = validateRequired(field);
            }

            if (!fieldValid) {
                isTabValid = false;
                if (!firstInvalidElement) {
                    firstInvalidElement = field; // Keep track of the first error
                }
            }
        });

        if (!isTabValid && firstInvalidElement) {
            firstInvalidElement.focus(); // Focus the first invalid field
        }

        return isTabValid;
    }

    // --- Event Listeners ---

    // Inline validation on blur/change
    form.querySelectorAll('input[required], textarea[required]').forEach(input => {
        if (input.type === 'email') {
            input.addEventListener('blur', () => validateEmail(input));
        } else if (input.type !== 'date' && input.type !== 'time') { // Date/Time have specific range checks on change
            input.addEventListener('blur', () => validateRequired(input));
        }
    });
    form.querySelectorAll('select[required]').forEach(select => {
        select.addEventListener('change', () => validateSelect(select));
    });
    
    // Tab Navigation
    function getCurrentTabIndex() {
        return Array.from(tabTriggers).findIndex(trigger => trigger.classList.contains('active'));
    }

    // Prevent direct clicking on top tabs & disable them visually
    tabTriggers.forEach((trigger, index) => {
        // Still prevent default just in case
        trigger.addEventListener('click', function (event) {
            event.preventDefault(); 
        });
        // Disable the button itself, except for the first one initially
        if (index > 0) { // Keep the first tab button enabled initially
             trigger.disabled = true;
        }
        // Add a class to visually indicate they are not directly clickable (optional)
        trigger.classList.add('disabled-tab-button'); 
    });
    
    // We also need to enable/disable tabs as we navigate with Next/Prev
    function updateTabButtonStates(activeIndex) {
         tabTriggers.forEach((trigger, index) => {
             // Allow clicking on already visited tabs or the current one
             if (index <= activeIndex) {
                 trigger.disabled = false;
                 trigger.classList.remove('disabled-tab-button');
             } else {
                 trigger.disabled = true;
                 trigger.classList.add('disabled-tab-button');
             }
         });
    }

    // --- Update existing navigation logic to call updateTabButtonStates ---
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const currentTabIndex = getCurrentTabIndex();
            if (validateTab(currentTabIndex)) {
                const nextTabIndex = currentTabIndex + 1;
                if (nextTabIndex < tabs.length) {
                    tabs[nextTabIndex].show(); 
                    updateTabButtonStates(nextTabIndex); // Update button states after showing tab
                }
            }
        });
    });

    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            const currentTabIndex = getCurrentTabIndex();
            const prevTabIndex = currentTabIndex - 1;
            if (prevTabIndex >= 0) {
                tabs[prevTabIndex].show();
                 updateTabButtonStates(prevTabIndex); // Update button states after showing tab
            }
        });
    });
    
    // Initial state update on load
    updateTabButtonStates(0);

    // --- Flavor Click Logic ---
    const flavorInput = document.getElementById('cake_flavor');
    const flavorItems = form.querySelectorAll('.clickable-flavor'); // Get all flavor items

    if (flavorInput && flavorItems.length > 0) {
        flavorItems.forEach(item => {
            item.addEventListener('click', function() {
                const clickedItem = this;
                const flavorName = clickedItem.dataset.flavor; // Get flavor from data attribute
                
                if (flavorName) {
                    let finalFlavorValue = flavorName; // Default to just the flavor name
                    
                    // Find the parent column and its header to get the base name
                    const column = clickedItem.closest('.col-md-4');
                    const header = column ? column.querySelector('.card-header') : null;
                    const baseName = header ? header.textContent.trim() : '';

                    // Prepend base name if it's Vanilla or Chocolate
                    if (baseName === 'Vanilla Base' || baseName === 'Chocolate Base') {
                        finalFlavorValue = baseName + ' / ' + flavorName;
                    }
                    
                    // Update the input field value
                    flavorInput.value = finalFlavorValue;

                    // Remove active class from all items first
                    flavorItems.forEach(i => i.classList.remove('active-flavor'));
                    // Add active class to the clicked item
                    clickedItem.classList.add('active-flavor');

                    // Trigger change event for validation and potentially other listeners
                    flavorInput.dispatchEvent(new Event('change'));
                    // Trigger blur to ensure required validation runs if field was empty
                    flavorInput.dispatchEvent(new Event('blur'));
                    // Optional: scroll to the input field if needed
                    // flavorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });
    }
    // --- End Flavor Click Logic ---

    // --- Image Preview Logic ---
    const imageInput = document.getElementById('decoration_images');
    const previewContainer = document.getElementById('image-preview-container');
    const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes

    if (imageInput && previewContainer) {
        imageInput.addEventListener('change', function(event) {
            previewContainer.innerHTML = ''; // Clear previous previews
            const files = event.target.files;
            let fileError = false;

            if (files.length > 0) {
                Array.from(files).forEach(file => {
                    if (file.size > maxFileSize) {
                        alert(`File "${file.name}" exceeds the 5MB size limit.`);
                        fileError = true;
                        return; // Skip this file
                    }

                    if (!file.type.startsWith('image/')){
                        alert(`File "${file.name}" is not a valid image type.`);
                        fileError = true;
                        return; // Skip this file
                    }

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.style.maxWidth = '100px';
                        imgElement.style.maxHeight = '100px';
                        imgElement.style.objectFit = 'cover'; // Or 'contain'
                        imgElement.style.borderRadius = '4px';
                        imgElement.alt = file.name; // Add alt text
                        previewContainer.appendChild(imgElement);
                    }
                    reader.readAsDataURL(file);
                });

                // If any file had an error, clear the input
                if (fileError) {
                    imageInput.value = ''; // Clear the selected files
                    previewContainer.innerHTML = ''; // Clear previews again
                }
            }
        });
    }
    // --- End Image Preview Logic ---

}); // End of the main DOMContentLoaded listener
</script>
@endpush

@push('styles')
<style>
    .clickable-flavor.active-flavor {
        background-color: #cfe2ff; /* Bootstrap primary-light color */
        font-weight: bold;
        border-left: 3px solid #0d6efd; /* Bootstrap primary color */
        padding-left: calc(1rem - 3px); /* Adjust padding to account for border */
    }
    /* Add transition for smoother effect (optional) */
    .clickable-flavor {
        transition: background-color 0.2s ease-in-out, border-left 0.2s ease-in-out;
    }
</style>
@endpush