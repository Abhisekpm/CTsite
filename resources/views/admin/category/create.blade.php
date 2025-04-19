@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Create Category</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('category') }}">All Category</a></li>
                            <li class="breadcrumb-item active">Create Category</li>
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
                            <form action="{{ route('category/store') }}" method="POST">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Title <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title" onload="convertToSlug(this.value)"
                                        onkeyup="convertToSlug(this.value)" placeholder="Enter Title Here">
                                    </div>
                                    <div class="form-group">
                                        <label>Slug <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="slug" id="slug" placeholder="Enter Slug Here">
                                    </div>
                                    {{-- <div class="form-group">
                                        <p class="settings-label">Image <span class="star-red">*</span></p>
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="image" id="file"
                                                onchange="loadFile(event)" class="hide-input">
                                            <label for="file" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        <h6 class="settings-size">Recommended image size is <span>150px x
                                                150px</span></h6>
                                        <div class="upload-images">
                                            <img src="{{ URL::to('assets/img/logo.png') }}" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                    </div> --}}
                                    <div class="form-group">
                                        <label>Description <span class="star-red">*</span></label>
                                        <textarea class="ckeditor form-control" name="description" id="description"></textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <div class="settings-btns">
                                            <button type="submit" class="btn btn-orange">Create</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">SEO Details</h5>
                        </div>
                        <div class="card-body pt-0">
                            <form>
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Meta Title<span class="star-red">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Meta Title Here">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Keywords<span class="star-red">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Meta Keywords Here">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Description <span class="star-red">*</span></label>
                                        <textarea type="text" class="form-control">Enter Meta Description Here</textarea>
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
                </div> --}}
            </div>
        </div>
    </div>
@endsection
