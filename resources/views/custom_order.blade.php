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
                                            @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Your Email" value="{{ old('email') }}" required>
                                            <label for="email">Your Email</label>
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- Phone --}}
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Your Phone Number" value="{{ old('phone') }}" required>
                                            <label for="phone">Phone Number</label>
                                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- Pickup Date --}}
                                    <div class="col-md-6">
                                        <div class="form-floating date" id="date3" data-target-input="nearest">
                                            <input type="date" class="form-control @error('pickup_date') is-invalid @enderror" id="pickup_date" name="pickup_date" placeholder="Pickup Date" value="{{ old('pickup_date') }}" required />
                                            <label for="pickup_date">Date of Cake Pickup</label>
                                            @error('pickup_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- Pickup Time --}}
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="time" class="form-control @error('pickup_time') is-invalid @enderror" id="pickup_time" name="pickup_time" placeholder="Time of Pickup" value="{{ old('pickup_time') }}" min="11:00" max="19:00">
                                            <label for="pickup_time">Time of Pickup (11am - 7pm)</label>
                                            @error('pickup_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
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
                                                    <option value='6"' {{ old('cake_size') == '6"' ? 'selected' : '' }}>6" Round (Serves 10)</option>
                                                    <option value='8"' {{ old('cake_size') == '8"' ? 'selected' : '' }}>8" Round (Serves 15)</option>
                                                    <option value='9"' {{ old('cake_size') == '9"' ? 'selected' : '' }}>9" Round (Serves 25)</option>
                                                    <option value='10"' {{ old('cake_size') == '10"' ? 'selected' : '' }}>10" Round (Serves 35)</option>
                                                </optgroup>
                                                <optgroup label="Double Tier">
                                                    <option value='6" on 8"' {{ old('cake_size') == '6" on 8"' ? 'selected' : '' }}>6" on 8" (Serves 30-35)</option>
                                                    <option value='6" on 9"' {{ old('cake_size') == '6" on 9"' ? 'selected' : '' }}>6" on 9" (Serves 40-45)</option>
                                                    <option value='6" on 10"' {{ old('cake_size') == '6" on 10"' ? 'selected' : '' }}>6" on 10" (Serves 50-55)</option>
                                                </optgroup>
                                                 <option value="Other" {{ old('cake_size') == 'Other' ? 'selected' : '' }}>Other (Describe in Notes)</option>
                                            </select>
                                            <label for="cake_size">Cake Size Selection</label>
                                            @error('cake_size') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                <div class="row g-3">
                                    {{-- Cake Flavor Input --}}
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('cake_flavor') is-invalid @enderror" id="cake_flavor" name="cake_flavor" placeholder="Cake Flavor" value="{{ old('cake_flavor') }}" required>
                                            <label for="cake_flavor">Enter Desired Cake Flavor (See guide below or <a href="{{ route('cakes-menu') }}">menu</a>)</label>
                                            @error('cake_flavor') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Fresh Fruit Cakes</h6>
                                                            <small class="text-muted d-block">(choose from either litchi,mango, strawberry, pineapple)</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Caramel Mousse</h6>
                                                            <small class="text-muted d-block">with Strawberry</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">White Chocolate Mousse</h6>
                                                            <small class="text-muted d-block">with Raspberry Compote</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Coconut Mousse</h6>
                                                            <small class="text-muted d-block">with Fresh Mango</small>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Butterscotch</h6>
                                                            <small class="text-muted d-block">(Caramel and Nougat)</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Victoria</h6>
                                                            <small class="text-muted d-block">(Raspberry and Strawberry Compote)</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Lemon Pineapple</h6>
                                                            <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Pinacolada</h6>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Tiramisu</h6>
                                                            <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Russian Honey</h6>
                                                             <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Fig & Honey</h6>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Custard Orange Nougat</h6>
                                                            <small class="text-danger d-block">Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item">
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
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Chocolate Cake soaked in Coffee</h6>
                                                            <small class="text-muted d-block">with Chocolate or Nutella Mousse</small>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Chocolate Cake</h6>
                                                            <small class="text-muted d-block">with Chocolate Orange Nougat</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">White Chocolate Mousse</h6>
                                                            <small class="text-muted d-block">with Strawberries</small>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Cold Coffee</h6>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Chocolate and Mint Mousse</h6>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Cookies and Cream Mousse or Buttercream</h6>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Peanut Buttercream or Mousse</h6>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Black Forest</h6>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">German Chocolate Cake</h6>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Hot Chocolate Cake</h6>
                                                            <small class="text-muted d-block">with Marshmallows</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Millionaire cake</h6>
                                                            <small class="text-muted d-block">(Chocolate Buttercream, Caramel and Crispy Cookie Crunch)</small>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Hazelnut Concord</h6>
                                                            <small class="text-muted d-block">(Chocolate Mousse with Crispy Hazelnut Meringue)</small>
                                                            <small class="text-danger d-block">Cannot be custom decorated, Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Sacher Torte</h6>
                                                            <small class="text-muted d-block">(Apricot Jam and Chocolate Glaze)</small>
                                                             <small class="text-danger d-block">Cannot be custom decorated, Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item">
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
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Rasmalai Tres Leches</h6>
                                                            <small class="text-danger d-block">Cannot be Custom Decorated, Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Rose Tres Leches</h6>
                                                             <small class="text-danger d-block">Cannot be Custom Decorated, Cannot be Eggless</small>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h6 class="mb-0">Litchi Tres Leches</h6>
                                                            <small class="text-danger d-block">Cannot be Custom Decorated, Cannot be Eggless</small>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Gulab Jamun</h6>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Gajar Halwa</h6>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Paan</h6>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Thandai</h6>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Rabdi</h6>
                                                        </li>
                                                         <li class="list-group-item">
                                                            <h6 class="mb-0">Mango Rabdi</h6>
                                                        </li>
                                                        <li class="list-group-item">
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
                                            <input type="text" class="form-control @error('message_on_cake') is-invalid @enderror" id="message_on_cake" name="message_on_cake" placeholder="Message on Cake (Optional)" value="{{ old('message_on_cake') }}">
                                            <label for="message_on_cake">Message on Cake (Optional)</label>
                                            @error('message_on_cake') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- Custom Decoration Textarea --}}
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('custom_decoration') is-invalid @enderror" placeholder="Describe custom decoration requirements (Optional)" id="custom_decoration" name="custom_decoration" style="height: 100px">{{ old('custom_decoration') }}</textarea>
                                            <label for="custom_decoration">Custom Decoration (Describe requirement, leave blank if standard)</label>
                                            @error('custom_decoration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- Decoration Image Upload --}}
                                    <div class="col-12">
                                         <label for="decoration_image" class="form-label">Upload Inspiration Photo (Optional)</label>
                                         <input class="form-control @error('decoration_image') is-invalid @enderror" type="file" id="decoration_image" name="decoration_image" accept="image/*">
                                         @error('decoration_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                         <small class="form-text text-muted">Max file size: 2MB. Allowed types: jpg, png, gif.</small>
                                    </div>
                                    {{-- Navigation & Submit --}}
                                     <div class="col-6 text-start mt-3">
                                         <button class="btn btn-secondary py-2 px-4 btn-prev" type="button"><i class="fas fa-arrow-left me-1"></i> Previous</button>
                                     </div>
                                    <div class="col-6 text-end mt-3">
                                        <button class="btn btn-primary w-100 py-3" type="submit">Submit Order Request</button>
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
    const nextButtons = form.querySelectorAll('.btn-next');
    const prevButtons = form.querySelectorAll('.btn-prev');
    const tabTriggers = form.querySelectorAll('#orderTabs button[data-bs-toggle="tab"]');

    // Create Bootstrap Tab instances for programmatic control
    const tabs = Array.from(tabTriggers).map(trigger => new bootstrap.Tab(trigger));

    function getCurrentTabIndex() {
        return Array.from(tabTriggers).findIndex(trigger => trigger.classList.contains('active'));
    }

    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const currentTabIndex = getCurrentTabIndex();
            const nextTabIndex = currentTabIndex + 1;
            // Optional: Add validation for current tab before proceeding
            // if (validateTab(currentTabIndex)) {
                if (nextTabIndex < tabs.length) {
                    tabs[nextTabIndex].show(); // Use Bootstrap's show method
                }
            // }
        });
    });

    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
             const currentTabIndex = getCurrentTabIndex();
            const prevTabIndex = currentTabIndex - 1;
            if (prevTabIndex >= 0) {
                tabs[prevTabIndex].show(); // Use Bootstrap's show method
            }
        });
    });

    // Optional: Add validation logic here
    // function validateTab(index) {
    //     let isValid = true;
    //     const currentPane = form.querySelector(tabTriggers[index].getAttribute('data-bs-target'));
    //     const inputs = currentPane.querySelectorAll('input[required], select[required], textarea[required]');
    //     inputs.forEach(input => {
    //         if (!input.value) {
    //             // Add error indication, maybe focus?
    //             console.log(input.id + ' is required');
    //             isValid = false;
    //         }
    //     });
    //     return isValid;
    // }
});
</script>
@endpush