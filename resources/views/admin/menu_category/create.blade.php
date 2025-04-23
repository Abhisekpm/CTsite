@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Create Category</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('menu-category') }}">All Categories</a></li>
                            <li class="breadcrumb-item active">Create Category</li>
                        </ul>
                    </div>
                </div>
            </div>

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
                            <form action="{{ route('menu/category/store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- Display Validation Errors --}}
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Title <span class="star-red">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Title Here" name="name" id="name" value="{{ old('name') }}" onload="convertToSlug(this.value)"
                                        onkeyup="convertToSlug(this.value)">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Slug <span class="star-red">*</span></label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror" placeholder="Enter Slug Here" name="slug" id="slug" value="{{ old('slug') }}">
                                        @error('slug')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Order <span class="star-red">*</span></label>
                                        {{-- <input type="number" class="form-control" placeholder="Enter Order Here" name="orderby" id="orderby"> --}}
                                        <input type="number" class="form-control @error('order') is-invalid @enderror" placeholder="Enter Order Here" name="order" id="order" value="{{ old('order') }}">
                                        @error('order')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <p class="settings-label">Image</p> {{-- Removed required star, as controller doesn't require it --}}
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="image" id="image" onchange="loadFile(event)"
                                                class="hide-input @error('image') is-invalid @enderror">
                                            <label for="file" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        @error('image')
                                            <span class="invalid-feedback d-block" role="alert"> {{-- Use d-block for file inputs --}}
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <h6 class="settings-size">Recommended image size is <span>150px x
                                                150px</span></h6>
                                        {{-- Image preview logic remains the same --}}
                                        <div class="upload-images" id="imagediv" style="display:none">
                                            <img id="previewImg" src="" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="ckeditor form-control" name="description" id="description">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                {{-- SEO Details Section --}}
                                <div class="card-header">
                                    <h5 class="card-title">SEO Details</h5>
                                </div>
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" placeholder="Enter Meta Title Here" name="meta_title" id="meta_title" value="{{ old('meta_title') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Keywords</label>
                                        <input type="text" class="form-control" placeholder="Enter Meta Keywords Here" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea class="form-control" name="meta_description" id="meta_description">{{ old('meta_description') }}</textarea>
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
            </div>
        </div>
    </div>
@endsection
