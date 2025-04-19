@extends('frontend.layout.main')
@section('title', 'Our Album - Chocolate Therapy')
@section('meta_keywords', '')
@section('meta_description', '')
@section('image', asset(''))
@section('main')
<div class="container-xxl py-5 bg-dark hero-header mb-5">
    <div class="container text-center mt-5 pt-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown">@if($search) {{$search}} @else Our Album @endif</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">@if($search) {{$search}} @else Our Album @endif</li>
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
        <h1 class="mb-5">@if($search) {{$search}} @else Our Album @endif</h1>
    </div>
    <div class="row mt-30 justify-content-end">
        <div class="col-md-3 col-12">
            <form>
                <div class="input-group mb-3">
                  <input type="text" class="form-control" name="search" placeholder="Search Album.." value="<?php if($search != ''){echo $search;} ?>" aria-label="Search Album..">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary py-2 px-4" type="button">Search</button>
                  </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row g-4 mt-30">
        @foreach ($ccat as $coll)
        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
            <a @if($search) data-bs-toggle="modal" href="#model_{{$coll->id}}" role="button" @else href="{{url('album/'.$coll->slug)}}" @endif>
                <div class="text-center rounded overflow-hidden">
                    <div class=" overflow-hidden ">
                        <img class="img-fluid" src="{{asset('assets/gallery/'.$coll->image)}}" alt="">
                    </div>
                    <div class="bg-primary p-10-0">
                        <h3 style="margin-bottom:0">{{$coll->name}}</h3>
                    </div>
                </div>
            </a>
        </div>
        @if($allcat != '')
        @foreach ($allcat as $cat)
            @if ($cat->id == $coll->gal_cat_id)
                @if ($cat->name === 'Classic Cakes')
                <div class="modal fade" id="model_{{$coll->id}}" aria-hidden="true" aria-labelledby="model_{{$coll->id}}" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel">{{$coll->name}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        {!! $coll->description !!}
                      </div>
                    </div>
                  </div>
                </div>
                @endif
            @endif
        @endforeach
        @endif
        @endforeach
    </div>
</div>
</div>
@endsection
