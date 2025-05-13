@extends('frontend.layout.main')
@section('title', $settings->website_name)
@section('meta_keywords', $settings->meta_keywords)
@section('meta_description', $settings->meta_description)
@section('image', asset(''))
@section('main')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<div class="container-xxl py-5 bg-dark hero-header-home mb-5">
    <div class="container mt-20 py-5">
        <div class="row align-items-center my-5 g-5 fix-header">
            <div class="col-lg-6 text-center text-lg-start pl-10" style="margin-top: -30px">
                <h1 class="display-3 my-5 text-white animated slideInLeft handwriting-font typewriters">The Secret Ingredient is Always Love</h1>
                <a href="{{ route('custom-order.create') }}" class="btn btn-primary py-sm-3 px-sm-5 me-3 animated slideInLeft">Order Now</a>
            </div>
            <div class="col-lg-6">

                <div class="owl-carousel hero-carousel">
                    <div class="hero-item bg-transparent rounded" style="padding:5px">
                        <img src="{{asset('assets/img/chocolate-1.jpg')}}" >
                    </div>
                    <div class="hero-item bg-transparent rounded" style="padding:5px">
                        <img src="{{asset('assets/img/chocolate-2.jpg')}}" >
                    </div>
                    <div class="hero-item bg-transparent rounded" style="padding:5px">
                        <img src="{{asset('assets/img/chocolate-3.jpg')}}" >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Navbar & Hero End -->


<!-- About Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 justify-content-center align-items-center">
            <!-- <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="img/about-1.jpg">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.3s"
                            src="img/about-2.jpg" style="margin-top: 25%;">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s"
                            src="img/about-3.jpg">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s"
                            src="img/about-4.jpg">
                    </div>
                </div>
            </div> -->
            <div class="col-lg-6">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">About Us</h5>
                <h1 class="mb-4">Welcome to <img src="{{ asset('assets/img/logo-black.png') }}" width="200"
                        height="auto" /></h1>
                <p class="mb-4">Back in the years, when people used to nibble on those baked goodies at family
                    dos and exclaimed "Who made this?!" odds were that the answer would be Nupur.</p>
                <p class="mb-4">From a sweet toothed kid baking cakes for her siblings to churning out
                    masterpieces from her West Chester home, Nupur Kundalia's love for art and pastry has come a
                    long way, culminating into Chocolate Therapy, a one stop shop for artisan desserts.</p>
                <!-- <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">5
                            </h1>
                            <div class="ps-4">
                                <p class="mb-0">Years of</p>
                                <h6 class="text-uppercase mb-0">Experience</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">8
                            </h1>
                            <div class="ps-4">
                                <p class="mb-0">Popular</p>
                                <h6 class="text-uppercase mb-0">Master Chefs</h6>
                            </div>
                        </div>
                    </div>
                </div> -->
                <a class="btn btn-primary py-3 px-5 mt-2" href="{{ url('about-us') }}">Read More</a>
            </div>
        </div>
    </div>
</div>
<!-- About End -->


<!-- Service Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item rounded pt-3 h-100">
                    <div class="p-4">
                        <i class="fa fa-3x fa-user-tie text-primary mb-4"></i>
                        <h5>Made from Scratch</h5>
                        <p>I approach baking with an emphasis on fresh ingredients and exciting flavors inspired by international cultures and my Indian heritage!</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item rounded pt-3 h-100">
                    <div class="p-4">
                        <i class="fa fa-3x fa-utensils text-primary mb-4"></i>
                        <h5>Food + Community</h5>
                        <p>Food has a special ability to bring people together. I hope you will welcome our small business in your hearts and community. For me, the secret ingredient to my baking is love!</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item rounded pt-3 h-100">
                    <div class="p-4">
                        <i class="fa fa-3x fa-cart-plus text-primary mb-4"></i>
                        <h5>Hometown Flavor</h5>
                        <p>I have been baking for a long time, and love the opportunity to experiment with a wide variety of flavors and ingredients. I work to incorporate local fresh indredients in my recipes. </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Service End -->

<!-- Menu Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Menu</h5>
            <h1 class="mb-5">Most Popular Items</h1>
        </div>
        <div class="row mt-20">
            <div class="col-md-4 text-center">
                <div class="food-menu-item h-100">
                    <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 active justify-content-center"
                        data-bs-toggle="pill">

                        <div class="ps-3">
                            <h4 class="mt-n1 mb-0">Rasmalai tres leches</h4>
                        </div>
                    </a>
                    <p>This is our all-time favorite! It is a fusion of the classic Mexican dessert of Tres Leches paired with the popular Indian sweet rasmalai. It has delicate infused flavors of cardamom and saffron. You wont want to miss it!</p>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="{{ url('our-menu') }}">See Menu</a>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="food-menu-item h-100">
                    <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 active justify-content-center"
                        data-bs-toggle="pill">

                        <div class="ps-3">
                            <h4 class="mt-n1 mb-0">Flavour Profiles</h4>
                        </div>

                    </a>
                    <p>We love to experiment with flavors! Our style combines European pastries, Indian flavors and the American classics. We try to incorporate different cultural influences in our baking. Stop by to experience the magic!</p>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="{{ url('our-menu') }}">See Menu</a>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="food-menu-item h-100">
                    <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 active justify-content-center"
                        data-bs-toggle="pill">

                        <div class="ps-3">
                            <h4 class="mt-n1 mb-0">Custom Decorations</h4>
                        </div>
                    </a>
                    <p>Our custom decorated cakes for your special occasions are the talk of the town. We cater to your whims and fancies when it comes to cake decorations. Our mantra is, if you can imagine it, we can create it.</p>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="{{ url('our-menu') }}">See Menu</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Menu End -->


{{-- <!-- Reservation Start -->
<div class="container-xxl py-5 px-0 wow fadeInUp" data-wow-delay="0.1s">
    <div class="row g-0">
        <div class="col-md-6">
            <div class="video">
                <button type="button" class="btn-play" data-bs-toggle="modal"
                    data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                    <span></span>
                </button>
            </div>
        </div>
        <div class="col-md-6 bg-dark d-flex align-items-center">
            <div class="p-5 wow fadeInUp" data-wow-delay="0.2s">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">Reservation</h5>
                <h1 class="text-white mb-4">Book A Table Online</h1>
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name"
                                    placeholder="Your Name">
                                <label for="name">Your Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email"
                                    placeholder="Your Email">
                                <label for="email">Your Email</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating date" id="date3" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" id="datetime"
                                    placeholder="Date & Time" data-target="#date3"
                                    data-toggle="datetimepicker" />
                                <label for="datetime">Date & Time</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="select1">
                                    <option value="1">People 1</option>
                                    <option value="2">People 2</option>
                                    <option value="3">People 3</option>
                                </select>
                                <label for="select1">No Of People</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Special Request" id="message" style="height: 100px"></textarea>
                                <label for="message">Special Request</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" type="submit">Book Now</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> --}}
@endsection
