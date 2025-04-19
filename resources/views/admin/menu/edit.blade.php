@extends('admin.layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Update Menu</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('menu') }}">All Menu</a></li>
                            <li class="breadcrumb-item active">Update Menu</li>
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
                            <form action="{{ url('admin/menu/update', [$menu->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="settings-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Title <span class="star-red">*</span></label>
                                                <input type="text" class="form-control" value="{{$menu->name}}"
                                                    name="name" id="name" onload="convertToSlug(this.value)"
                                                    onkeyup="convertToSlug(this.value)">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Category <span class="star-red">*</span></label>
                                                <select class="form-control" name="category">
                                                    <option value=" ">Select Category</option>
                                                    @foreach ($menu_cat as $cat)
                                                        <option value="{{$cat->id}}" @if($cat->id == $menu->menu_category_id) selected @endif>{{$cat->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="form-group">
                                        <label>Description (optional)</label>
                                        <textarea class="form-control" name="description" id="description">{{$menu->description}}</textarea>
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
    <!-- <script>
        const option1 = document.getElementById("addOption1");
        var count = 0;
        const handleIncrement1 = () => {
            count++;

            //first
            var mydiv = document.getElementById("optionFields1");
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control mt-20';
            input.placeholder = 'Enter here';
            input.name = 'option_1[]' // Some dynamic name logic
            mydiv.appendChild(input);

            //second
            var mydiv2 = document.getElementById("optionFields2");
            const input2 = document.createElement('input');
            input2.type = 'text';
            input2.className = 'form-control mt-20';
            input2.placeholder = 'Enter here';
            input2.name = 'option_2[]' // Some dynamic name logic
            mydiv2.appendChild(input2);

            //third
            var mydiv3 = document.getElementById("optionFields3");
            const input3 = document.createElement('input');
            input3.type = 'text';
            input3.className = 'form-control mt-20';
            input3.placeholder = 'Enter here';
            input3.name = 'price_2[]' // Some dynamic name logic
            mydiv3.appendChild(input3);

            //fourth
            var mydiv4 = document.getElementById("optionFields4");
            const input4 = document.createElement('input');
            input4.type = 'text';
            input4.className = 'form-control mt-20';
            input4.placeholder = 'Enter big price here';
            input4.name = 'price_2[]' // Some dynamic name logic
            mydiv4.appendChild(input4);

            document.getElementById('hidden_option_1').style.display = 'block';
            document.getElementById('hidden_option_2').style.display = 'block';
            document.getElementById('hidden_option_3').style.display = 'block';
        };
        option1.addEventListener("click", handleIncrement1);
    </script> -->
@endsection
