@extends('frontend.layout.main')
@section('title', 'Main Title')
@section('meta_keywords', '')
@section('meta_description', '')
@section('image', asset(''))
@section('main')
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center mt-5 pt-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Blogs</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page"><a href="{{ url('blogs') }}">Blogs</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s" style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Blogs</h5>
                <h1 class="mb-5">Lorem Ipsum</h1>
            </div>
            <div class="row g-10 align-items-center mt-30">
                @foreach ($blogs as $key=>$list)
                <div class="col-lg-3">
                    <div class="card blogs">
                        <a href="{{url('blogs/'.$list->slug)}}"><img class="card-img-top" src="{{ asset('assets/blog_img/'.$list->image) }}" alt="Card image cap"></a>
                        <div class="card-body">
                            <a href="{{url('blogs/'.$list->slug)}}"><h5 class="card-title">{{$list->title}}</h5></a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>


@endsection
