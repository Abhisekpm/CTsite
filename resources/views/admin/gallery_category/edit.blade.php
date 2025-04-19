@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Update Collection Category</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('ccat') }}">All Album Category</a></li>
                            <li class="breadcrumb-item active">Update Album Category</li>
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
                            <form action="{{ url('admin/ccat/update', [$ccat->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Name <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="name" value="{{$ccat->name}}" onload="convertToSlug(this.value)"
                                        onkeyup="convertToSlug(this.value)">
                                    </div>
                                    <div class="form-group">
                                        <label>Slug <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="slug" id="slug" value="{{$ccat->slug}}">
                                    </div>
                                    <div class="form-group">
                                        <p class="settings-label">Image <span class="star-red">*</span></p>
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="image" id="file"
                                                onchange="loadFile(event)" class="hide-input" value="{{ $ccat->image }}">
                                            <label for="file" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        <h6 class="settings-size">Recommended image size is <span>150px x
                                                150px</span></h6>
                                        <div class="upload-images">
                                            <img id="previewImg" src="{{ URL::to('assets/gallery/'.$ccat->image) }}" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                        <input type="hidden" name="hidden_image" value="{{ $ccat->image }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Order <span class="star-red">*</span></label>
                                        <input class="form-control" name="order" type="number" id="order" value="{{$ccat->order}}">
                                    </div>
                                </div>
                                <div class="card-header">
                                    <h5 class="card-title">SEO Details</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <form>
                                        <div class="settings-form">
                                            <div class="form-group">
                                                <label>Meta Title<span class="star-red">*</span></label>
                                                <input type="text" class="form-control" name="meta_title" value="{{$ccat->meta_title}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Meta Keywords<span class="star-red">*</span></label>
                                                <input type="text" class="form-control" name="meta_keywords" value="{{$ccat->meta_keywords}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Meta Description <span class="star-red">*</span></label>
                                                <textarea type="text" class="form-control" name="meta_description">{{$ccat->meta_description}}</textarea>
                                            </div>
                                            <div class="form-group mb-0">
                                                <div class="settings-btns">
                                                    <button type="submit" class="btn btn-orange">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
