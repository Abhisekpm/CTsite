@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Create Album Category</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('collection') }}">All Album</a></li>
                            <li class="breadcrumb-item active">Create Collection Category</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Basic Details</h5>
                        </div>
                        <div class="card-body pt-0">
                            <form action="{{ route('ccat/store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Name <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Name Here" name="name" id="name" onload="convertToSlug(this.value)"
                                        onkeyup="convertToSlug(this.value)">
                                    </div>
                                    <div class="form-group">
                                        <label>Slug <span class="star-red">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Slug Here" name="slug" id="slug">
                                    </div>
                                    <div class="form-group">
                                        <p class="settings-label">Image <span class="star-red">*</span></p>
                                        <div class="settings-btn">
                                            <input type="file" accept="image/*" name="image" id="image" onchange="loadFile(event)"
                                                class="hide-input">
                                            <label for="file" class="upload">
                                                <i class="feather-upload"></i>
                                            </label>
                                        </div>
                                        <h6 class="settings-size">Recommended image size is <span>150px x
                                                150px</span></h6>
                                        <div class="upload-images" id="imagediv" style="display:none">
                                            <img id="previewImg" src="" alt="Image">
                                            <a href="javascript:void(0);" class="btn-icon logo-hide-btn">
                                                <i class="feather-x-circle"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Order <span class="star-red">*</span></label>
                                        <input type="number" class="form-control" name="order" id="order">
                                    </div>
                                </div>
                                <div class="card-header">
                                    <h5 class="card-title">SEO Details</h5>
                                </div>
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" placeholder="Enter Meta Title Here" name="meta_title" id="meta_title">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Keywords</label>
                                        <input type="text" class="form-control" placeholder="Enter Meta Keywords Here" name="meta_keywords" id="meta_keywords">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea class="form-control" name="meta_description" id="meta_description"></textarea>
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
