@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Settings</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('setting/page') }}">Settings</a></li>
                            <li class="breadcrumb-item active">General Settings</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="settings-menu-links">
                <ul class="nav nav-tabs menu-tabs">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{url('admin/setting/page')}}">General Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('admin/setting/contact')}}">Contact</a>
                    </li>
                </ul>
            </div>
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Website Basic Details</h5>
                        </div>
                        <div class="card-body pt-0">
                            <form action="{{ url('admin/setting/page/updatebasic', [$settings->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Website Title </label>
                                        <input type="text" class="form-control" name="website_name" @if ($settings === null)placeholder="Enter Website Name Here" @else value="{{$settings->website_name}}"@endif>
                                    </div>
                                    <div class="form-group">
                                        <p class="settings-label">Logo </p>
                                        <div class="settings-btn">
                                            <input type="file" name="logo" id="logo"
                                                onchange="loadFile(event)" class="hide-input">
                                            <label for="file" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        <h6 class="settings-size">Recommended image size is <span>150px x
                                                150px</span></h6>
                                        <div class="upload-images">
                                            <img src="@if ($settings === null){{ URL::to('assets/img/settings/logo-black.png') }}@else{{ URL::to('assets/img/settings/'.$settings->logo) }}@endif" alt="Image" id="previewImg">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                        <input type="hidden" name="hidden_logo" value="{{$settings->logo}}">
                                    </div>
                                    <div class="form-group">
                                        <p class="settings-label">Favicon </p>
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="favicon" id="file"
                                                onchange="loadFile(event)" class="hide-input">
                                            <label for="favicon" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        <input type="hidden" name="hidden_favicon" value="{{$settings->favicon}}">
                                        <h6 class="settings-size">
                                            Recommended image size is <span>16px x 16px or 32px x 32px</span>
                                        </h6>
                                        <h6 class="settings-size mt-1">Accepted formats: only png and ico</h6>
                                        <div class="upload-images upload-size">
                                            <img src="@if ($settings === null){{ URL::to('assets/img/favicon.png') }}@else{{ URL::to('assets/img/settings/'.$settings->favicon) }}@endif" alt="Image" id="previewImg">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">SEO Details</h5>
                        </div>
                        <div class="card-body pt-0">
                            <form action="{{ url('admin/setting/page/updateseo', [$settings->id]) }}" method="POST">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" name="meta_title" @if ($settings === null)placeholder="Enter Meta Title Here" @else value="{{$settings->meta_title}}"@endif>
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Keywords</label>
                                        <input type="text" class="form-control" name="meta_keywords" @if ($settings === null)placeholder="Enter Meta Keywords Here" @else value="{{$settings->meta_keywords}}"@endif>
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea type="text" class="form-control" name="meta_description">{{$settings->meta_description}}</textarea>
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
