@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Create Page</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('pages') }}">All Pages</a></li>
                            <li class="breadcrumb-item active">Create Page</li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Basic Details</h5>
                        </div>
                        <div class="card-body pt-0">
                            <form action="{{ route('pages/store') }}" method="POST">
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
                                    <div class="form-group">
                                        <label>Description <span class="star-red">*</span></label>
                                        <textarea class="ckeditor form-control" name="description" id="description"></textarea>
                                    </div>
                                    <div class="card-header mb-20">
                                        <h5 class="card-title mt-20">SEO Details</h5>
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="Enter Meta Title Here">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Keywords</label>
                                        <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" placeholder="Enter Meta Keywords Here">
                                    </div>
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea class="form-control" name="meta_description" placeholder="Enter Meta Description Here" id="meta_description"></textarea>
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
