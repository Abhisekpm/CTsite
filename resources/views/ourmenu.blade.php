@extends('frontend.layout.main')
@section('title', 'Our Menu - Chocolate Therapy')
@section('meta_keywords', '')
@section('meta_description', '')
@section('image', asset(''))
@section('main')
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center mt-5 pt-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">{{$menu_cat->name}}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{url('our-menu')}}">Menu</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">{{$menu_cat->name}}</li>
                </ol>
            </nav>
        </div>
    </div>
    </div>
    <!-- Navbar & Hero End -->


    <!-- Menu Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">{{$menu_cat->name}}</h5>
            </div>
            <div class="tab-class text-center wow fadeInUp mt-20" data-wow-delay="0.1s">
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row d-flex justify-content-center">
                                    @forelse ($menus as $menu)
                                    <div class="col-lg-@if($menus->count() > 1)6 @elseif($menus->count() == 1)6 @endif"  style="margin-bottom: 20px">
                                        <div class="shadow br-0-0-10-10 border-bottom-thick h-100">
                                            <div class="mt-30">
                                            <div class="bg-primary br-10-10-0-0 p-10-0">
                                                <h3 style="margin-bottom:0">{{$menu->name}}</h3>
                                            </div>
                                            <div class="p-20">
                                                @if ($menu->description)
                                                <div class="pb-20 text-start">
                                                    <div
                                                        class="w-100">
                                                        <h6 class="pb-2 m-b-20">
                                                            {!! $menu->description !!}
                                                        </h6>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    @empty

                                    @endforelse
                                    <div class="col-lg-12" style="padding-top:40px">
                                        
                                        {{-- <div class="text-center wow fadeInUp mt-30" data-wow-delay="0.1s">
                                            <h1 class="mb-30 mt-5">Choose from these Flavour Profiles</h1>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="shadow br-0-0-10-10">
                                                    <div class="bg-primary br-10-10-0-0 p-10-0">
                                                        <h3 style="margin-bottom:0">Vanilla Base</h3>
                                                    </div>
                                                    <div class="p-20">
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Fresh Fruit Cakes<br /><span
                                                                            class="sub-menu-items">(choose from either
                                                                            litchi,mango, strawberry,
                                                                            pineapple)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Caramel Mousse <br /><span
                                                                            class="sub-menu-items">with
                                                                            Strawberry</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>White Chocolate Mousse <br /><span
                                                                            class="sub-menu-items">with Raspberry
                                                                            Compote</span><span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Coconut Mousse<br /><span
                                                                            class="sub-menu-items">with Fresh
                                                                            Mango</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Butterscotch<br /><span
                                                                            class="sub-menu-items">(Caramel and
                                                                            Nougat)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Victoria<br /><span
                                                                            class="sub-menu-items">(Raspberry and
                                                                            Strawberry Compote)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Lemon Pineapple</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Pinacolada</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Tiramisu</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Russian Honey</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Fig & Honey</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Custard Orange Nougat</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Banana Cake<br /><span
                                                                            class="sub-menu-items">with
                                                                            Nutella or Peanut
                                                                            Butter Mousse</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="shadow br-0-0-10-10">
                                                    <div class="bg-primary br-10-10-0-0 p-10-0">
                                                        <h3 style="margin-bottom:0">Chocolate Base</h3>
                                                    </div>
                                                    <div class="p-20">
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Chocolate Cake soaked in Coffee<br /><span
                                                                            class="sub-menu-items">with Chocolate or
                                                                            Nutella Mousse</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Chocolate Cake<br /><span
                                                                            class="sub-menu-items">with Chocolate Orange
                                                                            Nougat</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>White Chocolate Mousse<br /><span
                                                                            class="sub-menu-items">with
                                                                            Strawberries</span><span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Cold Coffee</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Chocolate and Mint Mousse</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Cookies and Cream Mousse or Buttercream</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Peanut Buttercream or Mousse</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Black Forest</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>German Chocolate Cake</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Hot Chocolate Cake<br /><span
                                                                            class="sub-menu-items">with
                                                                            Marshmallows</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Hazelnut Concord<br /><span
                                                                            class="sub-menu-items">(Chocolate Mousse with
                                                                            Crispy Hazelnut Meringue)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Sachet Torte<br /><span
                                                                            class="sub-menu-items">(Chocolate Cake with
                                                                            Apricot Jam and Chocolate Glaze)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Chocolate Opera<br /><span
                                                                            class="sub-menu-items">(Almond Cake with Coffee
                                                                            Buttercream and Ganache)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Millionaire cake<br /><span
                                                                            class="sub-menu-items">(Chocolate Buttercream,
                                                                            Caramel and Crispy Cookie Crunch)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="shadow br-0-0-10-10">
                                                    <div class="bg-primary br-10-10-0-0 p-10-0">
                                                        <h3 style="margin-bottom:0">Indian Fusion Flavours</h3>
                                                    </div>
                                                    <div class="p-20">
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Rasmalai Tres Leches<br /><span
                                                                            class="sub-menu-items">(Cannot be Custom
                                                                            Decorated, Cannot be eggless)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Gulab Jamun</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Gajar Halwa</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Paan</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Litchi Tres Leches<br /><span
                                                                            class="sub-menu-items">(Cannot be Custom
                                                                            Decorated)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Thandai</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Rabdi</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Sitaphal<br /><span
                                                                            class="sub-menu-items">(Custard
                                                                            Apple)</span></span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="shadow br-0-0-10-10" style="padding-top:20px">
                                                    <div class="bg-primary br-10-10-0-0 p-10-0">
                                                        <h3 style="margin-bottom:0">Custom Decorations</h3>
                                                    </div>
                                                    <div class="p-20">
                                                        <div class="d-flex align-items-center pb-20 menu-list">
                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h6 class="d-flex text-center pb-2 m-b-20">
                                                                    <span>Describe the requirements for custom decorations
                                                                        for your cake. Feel free to use images and
                                                                        screenshots. We will quote you the additional cost
                                                                        for your custom decorations based on your
                                                                        requirements. Please keep in mind that the custom
                                                                        decorations are all hand crafted and made from
                                                                        scratch</span>
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="shadow br-0-0-10-10" style="padding-top:20px">
                                                    <div class="bg-primary br-10-10-0-0 p-10-0">
                                                        <h3 style="margin-bottom:0">Custom Ordering Instructions</h3>
                                                    </div>
                                                    <div class="p-20">
                                                        <div class="d-flex align-items-center pb-20 menu-list">
                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h6 class="d-flex text-center pb-2 m-b-20">
                                                                    <span>Send your requirements to <a
                                                                            href="tel:+12675418620">267-541-8620</a> via
                                                                        text, whatsapp or facebook messenger.</span>
                                                                </h6>
                                                                <h6 class="d-flex text-center pb-2 m-b-20">
                                                                    <span>Select the size, flavor and custom decorations for
                                                                        your cake using the options below.</span>
                                                                </h6>
                                                                <h6 class="d-flex text-center pb-2 m-b-20">
                                                                    <span>Describe the requirements for custom decorations
                                                                        using images or screenshots.</span>
                                                                </h6>
                                                                <h6 class="d-flex text-center pb-2 m-b-20">
                                                                    <span>We will quote the additional cost for the custom
                                                                        decorations.</span>
                                                                </h6>
                                                                <h6 class="d-flex text-center pb-2 m-b-20">
                                                                    <span>Let us know if we should know about any
                                                                        allergies.</span>
                                                                </h6>
                                                                <h6 class="d-flex text-center pb-2 m-b-20">
                                                                    <span>Please keep in mind that each cake and custom
                                                                        decoration is hand crafted and made from
                                                                        scratch.</span>
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="shadow br-0-0-10-10">
                                                    <div class="bg-primary br-10-10-0-0 p-10-0">
                                                        <h3 style="margin-bottom:0">Macaroons</h3>
                                                    </div>
                                                    <div class="p-20">
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Lemon</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Raspberry</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Cookies & cream</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Mocha</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Milk chocolate caramel</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Butter pecan</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Fig & cream cheese</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Chai</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Apple pie</span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Chocolate </span>
                                                                    <span class="text-primary">$2.50</span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="shadow br-0-0-10-10">
                                                    <div class="bg-primary br-10-10-0-0 p-10-0">
                                                        <h3 style="margin-bottom:0">Loafs</h3>
                                                    </div>
                                                    <div class="p-20">
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span></span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">Small</span>
                                                                        <span class="text-primary">Large</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Coffee almond</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Honey rose pistachio</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Lemon</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Mango rum</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Plum cake<br/><span
                                                                            class="sub-menu-items">( fruit and nut )</span></span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Marble</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Banana</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Pumpkin</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center pb-20 menu-list">

                                                            <div
                                                                class="w-100 d-flex flex-column text-start border-bottom-thick ps-4">
                                                                <h5 class="d-flex justify-content-between pb-2">
                                                                    <span>Pound cake</span>
                                                                    <span>
                                                                        <span class="text-primary" style="padding-right:30px">$12</span>
                                                                        <span class="text-primary">$17</span>
                                                                    </span>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-lg-12 mt-30">
                                <div class="bg-primary br-10 p-10-0 mt-30">
                                    <h4 class="ff-secondary text-black fw-normal"
                                        style="text-align: center !important; padding-top:20px">
                                        *Mousse = Soft Fresh Whipped Cream Filling</h4>
                                    <h4 class="ff-secondary text-black fw-normal"
                                        style="text-align: center !important; padding-top:20px">
                                        *Meringue = Crispy Egg White Topping</h4>
                                    <h4 class="ff-secondary text-black fw-normal"
                                        style="text-align: center !important; padding-top:20px">
                                        *Nougat = Crunchy Caramelized Sugar Topping</h4>
                                </div>

                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
