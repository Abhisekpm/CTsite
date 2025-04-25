<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', '')</title>
    @if (View::hasSection('meta_keywords'))
    <meta name="keywords" content="@yield('meta_keywords', '')">
    @endif
    <meta name="description" content="@yield('meta_description', '')">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#2e2a8f">
    @include('frontend.layout.style')
    
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-261673956-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-261673956-1');
</script>

    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @stack('styles') {{-- Added stack for page-specific styles --}}
</head>

<body>
    <div class="container-xxl bg-white p-0 full-banner">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->
        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="/" class="navbar-brand p-0">
                    <!--<h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>Restoran</h1>--->
                    <img src="{{ asset('assets/img/settings/'.$settings->logo) }}" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="{{ url('/') }}" class="nav-item nav-link {{set_active(['/'])}}">Home</a>
                        <a href="{{ url('about-us') }}" class="nav-item nav-link {{set_active(['about-us'])}}">About</a>
                        <a href="{{ url('our-menu') }}" class="nav-item nav-link {{set_active(['our-menu'])}}">Menu</a>
                        <a href="{{ url('album') }}" class="nav-item nav-link {{set_active(['album'])}}">Album</a>
                        <!--<a href="{{ url('blogs') }}" class="nav-item nav-link {{set_active(['blogs'])}}">Blogs</a>-->
                        <a href="{{ url('contact-us') }}" class="nav-item nav-link {{set_active(['contact-us'])}}">Contact</a>
                    </div>
                    <a href="{{ route('custom-order.create') }}" class="btn btn-primary py-2 px-4">Order Now</a>
                </div>
            </nav>
            @yield('main')
            
            {{-- Conditionally include testimonials --}}
            @if(Route::currentRouteName() !== 'custom-order.create')
                @include('frontend.layout.testimonial')
            @endif
            
            <!-- Footer Start -->
            <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
                <div class="container py-5">
                    <div class="row g-5">
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Company</h4>
                            <!--<a class="btn btn-link" href="{{ url('blogs') }}">Blogs</a>-->
                            <a class="btn btn-link" href="{{ url('about-us') }}">About Us</a>
                            <a class="btn btn-link" href="{{ url('contact-us') }}">Contact Us</a>
                            <a class="btn btn-link" href="{{ url('privacy-policy') }}">Privacy Policy</a>
                            <a class="btn btn-link" href="{{ url('terms-and-conditions') }}">Terms & Condition</a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Contact</h4>
                            <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i><a href="https://goo.gl/maps/UNuLiBbeyGxEKgNv6" target="_blank">16 Marchwood Rd, Exton, PA 19341</a>
                            </p>
                            <p class="mb-2"><i class="fa fa-phone-alt me-3"></i><a href="tel:{{$settings->contact_number}}">{{$settings->contact_number}}</a></p>
                            <p class="mb-2"><i class="fa fa-envelope me-3"></i><a href="mailto:{{$settings->contact_email}}">{{$settings->contact_email}}</a></p>
                            <div class="d-flex pt-2">
                                <div class="row">
                                    <div class="col-6">
                                        <a class="btn btn-outline-light btn-social" href="https://www.facebook.com/BitterSweetAffair/" target="_blank" rel="nofollow"><i
                                        class="fab fa-facebook-f"></i> &nbsp;&nbsp;Facebook</a>
                                    </div>
                                    <div class="col-6">
                                        <a class="btn btn-outline-light btn-social" href="https://www.instagram.com/chocolate.therapy.by.nupur/" target="_blank" rel="nofollow"><i
                                        class="fab fa-instagram"></i> &nbsp;&nbsp;Instagram</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Opening</h4>
                            <h5 class="text-light fw-normal">Monday (Closed)</h5>
                            <h5 class="text-light fw-normal">Tue – Thu : 11AM – 6PM</h5>
                            <h5 class="text-light fw-normal">Fri – Sat : 11AM – 7PM</h5>
                            <h5 class="text-light fw-normal">Sun : 11AM – 6PM</h5>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="offer-image mt60">
                                <div class="text-center bg-light">
	                               <h5 class="mb10 pt10">Find us on Facebook <b class="plusMinus"></b></h5>
	                               <div class="showHide_rp">                	
	                                   <iframe src="https://www.facebook.com/plugins/likebox.php?href=https://www.facebook.com/BitterSweetAffair&amp;width=330&amp;colorscheme=light&amp;show_faces=true&amp;stream=false&amp;header=false&amp;height=238" scrolling="no" frameborder="0" style="border:none;overflow:hidden; width:330px;height:130px; background: #FFF;"></iframe>	
	                               </div>
	                           </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="copyright">
                        <div class="row">
                            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                &copy; <a class="border-bottom" href="#">Chocolate Therapy</a>, All Right Reserved.

                                <!--/*** This template is free as long as you keep the footer author's credit link/attribution link/backlink. If you'd like to use the template without the footer author's credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                                Design & Developed By <a href="https://www.dartdigitalagency.com/" target="_blank">Dart Digital Agency</a>
                            </div>
                            <div class="col-md-6 text-center text-md-end">
                                <div class="footer-menu">
                                    <a href="{{ url('/') }}">Home</a>
                                    <a href="">Cookies</a>
                                    <a href="">Help</a>
                                    <a href="">FAQs</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->


            <!-- Back to Top -->
            <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i
                    class="bi bi-arrow-up"></i></a>
        </div>

        @include('frontend.layout.script')

        {{-- Flatpickr JS --}}
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        @stack('scripts')

<script type="text/javascript" src="https://popupsmart.com/freechat.js"></script><script> window.start.init({ title: "Hi there ✌️", message: "How may we help you? Just send us a message now to get assistance.", color: "#90D6E2", position: "left", placeholder: "Enter your message", withText: "Write with", viaWhatsapp: "Or write us directly via Whatsapp", gty: "Go to your", awu: "and write us", connect: "Connect now",  button: "Write us", device: "everywhere", logo: "https://d2r80wdbkwti6l.cloudfront.net/ZhPxyaBmxpVmubBCCVq8Bmg9TjXWIwUy.jpg",  services: [{"name":"whatsapp","content":"+12675418620"},{"name":"phone","content":"+12675418620"}]})</script>
</body>

</html>
