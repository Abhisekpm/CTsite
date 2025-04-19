@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Update Blog</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('blogs') }}">All Blogs</a></li>
                            <li class="breadcrumb-item active">Update Blog</li>
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
                            <form action="{{ url('admin/blog/update', [$blog->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Title <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="title" value="{{$blog->title}}" onload="convertToSlug(this.value)"
                                        onkeyup="convertToSlug(this.value)">
                                    </div>
                                    <div class="form-group">
                                        <label>Slug <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="slug" id="slug" value="{{$blog->slug}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Category <span class="star-red">*</span></label>
                                        <select class="form-control" name="category" id="category">
                                            <option>Select</option>
                                            @forelse ($categories as $category)
                                                <option value="{{$category->id}}" @if ($blog->category == $category->id) selected @endif>{{$category->title}}</option>
                                            @empty

                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <p class="settings-label">Image <span class="star-red">*</span></p>
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="image" id="file"
                                                onchange="loadFile(event)" class="hide-input" value="{{ $blog->image }}">
                                            <label for="file" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        <h6 class="settings-size">Recommended image size is <span>150px x
                                                150px</span></h6>
                                        @if($blog->image)
                                        <div class="upload-images">
                                            <img id="previewImg" src="{{ URL::to('assets/blog_img/'.$blog->image) }}" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                        @endif
                                        <input type="hidden" name="hidden_image" id="hidden_image" value="{{ $blog->image }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Description <span class="star-red">*</span></label>
                                        <textarea class="ckeditor form-control" name="description" id="description">{{$blog->description}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Tags <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" name="tags" placeholder="Enter Tags Here" value="{{$blog->slug}}">
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
                                                <input type="text" class="form-control" name="meta_title" value="{{$blog->meta_title}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Meta Keywords<span class="star-red">*</span></label>
                                                <input type="text" class="form-control" name="meta_keywords" value="{{$blog->meta_keywords}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Meta Description <span class="star-red">*</span></label>
                                                <textarea type="text" class="form-control" name="meta_description">{{$blog->meta_description}}</textarea>
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
