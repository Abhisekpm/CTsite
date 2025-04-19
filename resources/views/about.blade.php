@extends('frontend.layout.main')
@section('title', 'About Us - Chocolate Therapy')
@section('meta_keywords', '')
@section('meta_description', '')
@section('image', asset(''))
@section('main')
<div class="container-xxl py-5 bg-dark hero-header mb-5">
    <div class="container text-center mt-5 pt-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">About Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page"><a href="{{url('about-us')}}">About Us</a></li>
            </ol>
        </nav>
    </div>
</div>
 <!-- About Start -->
 <div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="row g-3 justify-content-center">
                    <div class="col-8 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="{{asset('assets/img/about-1.webp')}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">About Us</h5>
                <h1 class="mb-4">Welcome to <img src="{{asset('assets/img/logo-black.png')}}" width="200" height="auto" /></h1>
                <p class="mb-4">Back in the years, when people used to nibble on those baked goodies at family dos and exclaimed “Who made this?!” odds were that the answer would be Nupur.</p>

                <p class="mb-4">From a sweet toothed kid baking cakes for her siblings to churning out masterpieces from her West Chester home, Nupur Kundalia’s love for art and pastry has come a long way, culminating into Chocolate Therapy, a one stop shop for artisan desserts.</p>
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">15
                            </h1>
                            <div class="ps-4">
                                <p class="mb-0">Years of</p>
                                <h6 class="text-uppercase mb-0">Experience</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">5
                            </h1><h1 class="flex-shrink-0 display-5 text-primary mb-0">K</h1>
                            <div class="ps-4">
                                <p class="mb-0">Happy</p>
                                <h6 class="text-uppercase mb-0">Bellies</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <p>
                    With a degree from the ‘Art Institute of Philadelphia’ in bag along with some wondrous years as a cake decorator at ‘Desserts International’ bakery, her attention to detail is second to none and the result are exquisite masterpiece cakes you won’t want to cut it…until you taste the mouth-watering flavors for yourself. Each is layered and covered in beautiful blanket of sugar work, simple and complicated at once. Along with celebration cakes, you’ll find classic uptown pastries piped with tangles of lush cream, artisan gelatos’ ( double thumbs up here), buttery cookies, standout mousses, superb cocktail desserts, seasonal treats like a tiny, charming  passion fruit mousse .</p>

                <p>We could keep going, but you probably get the idea. Trust us, you’ll never have crumb here you won’t love.</p>

                <p>P.S: We also cater to your whims and fancies. Our mantra is, if you can imagine it, we can whip it up for you.
                </p>
            </div>
        </div>
    </div>
</div>
 <div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-12 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="{{asset('assets/img/about-2.webp')}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-12">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="{{asset('assets/img/about-3.webp')}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-12">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="{{asset('assets/img/about-4.webp')}}">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-12">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="{{asset('assets/img/about-5.jpeg')}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
