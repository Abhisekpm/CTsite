@extends('frontend.layout.main')
@section('title', 'Our Menu - Chocolate Therapy')
@section('meta_keywords', '')
@section('meta_description', '')
@section('image', asset(''))
@section('main')
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center mt-5 pt-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Menu</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Menu</li>
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
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Menu</h5>
                <!--<h1 class="mb-5">Pick Size Based on Serving Needs</h1>-->
            </div>
            <div class="tab-class text-center wow fadeInUp mt-20" data-wow-delay="0.1s">
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row justify-content-center">
                                    @foreach ($all_cat as $key=>$list)
                                    <div class="col-lg-3">
                                        <a href="{{url($list->slug)}}"><div class="shadow br-0-0-10-10 mb-30">
                                            <div class="bg-primary br-10-10-0-0 p-10-0">
                                                <h3 style="margin-bottom:0">{{$list->name}}</h3>
                                            </div>
                                            <div class="p-20">
                                                <div class="d-flex align-items-center pb-20">
                                                    <div class="w-100 d-flex justify-content-center text-center">
                                                        @if ($list->image)
                                                            <img src="{{ asset('assets/menu/'.$list->image) }}" width="100" />
                                                        @endif
                                                        <p>{{$list->description}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div></a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
