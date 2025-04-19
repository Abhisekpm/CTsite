@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Update Category</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('menu-category') }}">All Categories</a></li>
                            <li class="breadcrumb-item active">Update Category</li>
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
                            <form action="{{ url('admin/menu/category/update', [$menu_cat->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Title <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" value="{{ $menu_cat->name }}" name="name" id="name" onload="convertToSlug(this.value)"
                                        onkeyup="convertToSlug(this.value)">
                                    </div>
                                    <div class="form-group">
                                        <label>Slug <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" value="{{ $menu_cat->slug }}" name="slug" id="slug">
                                    </div>
                                    <div class="form-group">
                                        <label>Order <span class="star-red">*</span></label>
                                        <input type="number" class="form-control" value="{{ $menu_cat->orderby }}" name="orderby" id="orderby">
                                    </div>
                                    <div class="form-group">
                                        <p class="settings-label">Image</p>
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="image" value="{{ $menu_cat->image }}" id="image" onchange="loadFile(event)"
                                                class="hide-input">
                                            <label for="image" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        @if($menu_cat->image)
                                        <div class="upload-images">
                                            <img id="previewImg" src="{{ URL::to('assets/menu/'.$menu_cat->image) }}" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                        @else
                                       <div class="upload-images" id="imagediv" style="display:none">
                                            <img id="previewImg" src="" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                        @endif
                                        <input type="hidden" name="hidden_image" id="hidden_image" value="{{ $menu_cat->image }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="ckeditor form-control" name="description" id="description">{{ $menu_cat->description }}</textarea>
                                    </div>
                                </div>
                                <div class="card-header">
                                    <h5 class="card-title">SEO Details</h5>
                                </div>
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" value="{{ $menu_cat->meta_title }}" name="meta_title" id="meta_title">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Keywords</label>
                                        <input type="text" class="form-control" value="{{ $menu_cat->meta_keywords }}" name="meta_keywords" id="meta_keywords">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea class="form-control" name="meta_description" id="meta_description">{{ $menu_cat->meta_description }}</textarea>
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
