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
                            <li class="breadcrumb-item active">Contact</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="settings-menu-links">
                <ul class="nav nav-tabs menu-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('admin/setting/page')}}">General Settings</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="{{url('admin/setting/contact')}}">Contact</a>
                    </li>
                </ul>
            </div>
            {!! Toastr::message() !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Contact Details</h5>
                        </div>
                        <div class="card-body pt-0">
                            <form action="{{ url('admin/setting/contact', [$settings->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="form-group">
                                        <label>Contact Email </label>
                                        <input type="text" class="form-control" name="contact_email" @if ($settings === null)placeholder="Enter Contact Email Here" @else value="{{$settings->contact_email}}"@endif>
                                    </div>
                                    <div class="form-group">
                                        <label>Contact Number </label>
                                        <input type="text" class="form-control" name="contact_number" @if ($settings === null)placeholder="Enter Contact Number Here" @else value="{{$settings->contact_number}}"@endif>
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
