@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Update Testimonial</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('testimonials') }}">All Testimonials</a></li>
                            <li class="breadcrumb-item active">Update Testimonial</li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            {{-- <div class="settings-menu-links">
                <ul class="nav nav-tabs menu-tabs">
                    <li class="nav-item active">
                        <a class="nav-link" href="settings.html">General Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="localization-details.html">Localization</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payment-settings.html">Payment Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="email-settings.html">Email Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="social-settings.html">Social Media Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="social-links.html">Social Links</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="seo-settings.html">SEO Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="others-settings.html">Others</a>
                    </li>
                </ul>
            </div> --}}

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Basic Details</h5>
                        </div>
                        <div class="card-body pt-0">
                            <form action="{{ route('testimonial/update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Name <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{$testi->name}}">
                                        <input type="hidden" name="id" id="id" value="{{$testi->id}}">
                                    </div>
                                    {{-- <div class="form-group">
                                        <label>Profession <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="position" id="position" value="{{$testi->position}}">
                                    </div> --}}
                                    <div class="form-group">
                                        <p class="settings-label">Image</p>
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="image" id="file"
                                                onchange="loadFile(event)" class="hide-input" value="{{ $testi->image }}">
                                            <label for="file" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        <h6 class="settings-size">Recommended image size is <span>150px x
                                                150px</span></h6>
                                        @if($testi->image)
                                        <div class="upload-images" id="imagediv">
                                            <img id="previewImg" src="{{ asset('assets/testimonials/'.$testi->image) }}" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                        @endif
                                        <input type="hidden" name="hidden_image" id="hidden_image" value="{{ $testi->image }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Quote <span class="star-red">*</span></label>
                                        <textarea class="ckeditor form-control" name="quote" id="quote">{{$testi->quote}}</textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <div class="settings-btns">
                                            <button type="submit" class="btn btn-orange">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
