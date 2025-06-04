@extends('frontend.layout.main')
@section('title', 'Main Title')
@section('meta_keywords', '')
@section('meta_description', '')
@section('image', asset(''))
@section('main')
<div class="container-xxl py-5 bg-dark hero-header mb-5">
    <div class="container text-center mt-5 pt-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</div>
</div>
<!-- Navbar & Hero End -->


<!-- Contact Start -->
<div class="container-xxl py-5">
<div class="container">
    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
        <h5 class="section-title ff-secondary text-center text-primary fw-normal">Contact Us</h5>
        <h1 class="mb-5">Contact For Any Query</h1>
    </div>
    <div class="row g-4">
        <div class="col-12">
            <div class="row gy-4">
                <div class="col-md-4">
                    <h5 class="section-title ff-secondary fw-normal text-start text-primary">Email</h5>
                    <p><i class="fa fa-envelope-open text-primary me-2"></i><a href="mailto:chocolatetherapybynupur@gmail.com">chocolatetherapybynupur@gmail.com</a></p>
                </div>
                <div class="col-md-4">
                    <h5 class="section-title ff-secondary fw-normal text-start text-primary">Call</h5>
                    <p><i class="fa fa-envelope-open text-primary me-2"></i><a href="tel:+12675418620">+1 (267) 541-8620</a></p>
                </div>
                <div class="col-md-4">
                    <h5 class="section-title ff-secondary fw-normal text-start text-primary">Address</h5>
                    <p><a href="https://goo.gl/maps/UNuLiBbeyGxEKgNv6" target="_blank"><i class="fa fa-envelope-open text-primary me-2"></i>16 Marchwood Rd, Exton, PA 19341</a></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 wow fadeIn" data-wow-delay="0.1s">
            <iframe class="position-relative rounded w-100 h-100"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3054.213076507925!2d-75.64146217260627!3d40.04834561355121!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c6f4ba385243ef%3A0x9e3ee7a86329e965!2s16%20Marchwood%20Rd%2C%20Exton%2C%20PA%2019341%2C%20USA!5e0!3m2!1sen!2sin!4v1677149695458!5m2!1sen!2sin"
                frameborder="0" style="min-height: 350px; border:0;" allowfullscreen="" aria-hidden="false"
                tabindex="0"></iframe>
        </div>
    </div>
</div>
</div>
@endsection
