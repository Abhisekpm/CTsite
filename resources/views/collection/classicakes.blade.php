@include('frontend.layout.head')
@include('frontend.layout.menu')
<div class="container-xxl py-5 bg-dark hero-header mb-5">
    <div class="container text-center my-5 pt-5 pb-4">
        <h1 class="display-3 text-white mb-3 animated slideInDown">Classic Cakes</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Classic Cakes</li>
            </ol>
        </nav>
    </div>
</div>
</div>
<!-- Navbar & Hero End -->


<!-- Team Start -->
<div class="container-xxl pt-5 pb-3">
<div class="container">
    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
        <h5 class="section-title ff-secondary text-center text-primary fw-normal">What We Made</h5>
        <h1 class="mb-5">Classic Cakes</h1>
    </div>
    <div class="row g-4">
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
            <a href="{{url('class-cakes')}}">
                <div class="team-item text-center rounded overflow-hidden">
                    <div class=" overflow-hidden ">
                        <img class="img-fluid" src="{{asset('assets/img/gallary/classic1.jpg')}}" alt="">
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
            <a>
                <div class="team-item text-center rounded overflow-hidden">
                    <div class=" overflow-hidden ">
                        <img class="img-fluid" src="{{asset('assets/img/gallary/classic2.jpg')}}" alt="">
                    </div>
                </div>
            </a>

        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
            <a href="">
                <div class="team-item text-center rounded overflow-hidden">
                    <div class=" overflow-hidden ">
                        <img class="img-fluid" src="{{asset('assets/img/gallary/classic3.jpg')}}" alt="">
                    </div>
                </div>
            </a>

        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
            <a>
            <div class="team-item text-center rounded overflow-hidden">
                <div class=" overflow-hidden ">
                    <img class="img-fluid" src="{{asset('assets/img/gallary/classic4.jpg')}}" alt="">
                </div>
            </div>
        </a>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
            <a>
            <div class="team-item text-center rounded overflow-hidden">
                <div class=" overflow-hidden ">
                    <img class="img-fluid" src="{{asset('assets/img/gallary/classic5.jpg')}}" alt="">
                </div>
            </div>
        </a>
        </div>
        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
            <a>
            <div class="team-item text-center rounded overflow-hidden">
                <div class=" overflow-hidden ">
                    <img class="img-fluid" src="{{asset('assets/img/gallary/classic6.jpg')}}" alt="">
                </div>
            </div>
        </a>
        </div>
    </div>
</div>
</div>
@include('frontend.layout.footer')
