@extends('admin.layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">All Category</h3>
                    <ul class="breadcrumb">
                        {{-- <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> --}}
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Category</li>
                    </ul>
                </div>
            </div>
        </div>
        {{-- message --}}
        {!! Toastr::message() !!}
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="page-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="page-title">Category List</h3>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    <a href="{{route('menu/category/create')}}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                <thead class="student-thread">
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Slug</th>
                                        <th>Order</th>
                                        <th>Image</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $num = 1
                                    @endphp
                                    @foreach ($menu_cat as $key=>$list )
                                    <tr>
                                        <td>{{$num}}</td>
                                        <input type="hidden" class="menuCat_id" value="{{$list->id}}">
                                        <td>
                                            @if (Str::lower($list->name) == 'cake' || Str::lower($list->name) == 'cakes')
                                                <i class="fa-regular fa-cake-candles me-2" style="color: var(--primary);"></i>
                                            @endif
                                            {{ $list->name }}
                                        </td>
                                        <td>{{ $list->slug }}</td>
                                        <td>{{ $list->order }}</td>
                                        <td>@if ($list->image)<img src="{{ asset('assets/menu/'.$list->image) }}" width="80" />@endif</td>
                                        <td>{{ \Carbon\Carbon::parse($list->created_at)->format('d F Y') }}</td>
                                        <td class="text-end">
                                            <div class="actions">
                                                <a href="{{ url('admin/menu/category/edit/'.$list->id) }}"class="btn btn-sm bg-danger-light" style="margin-right: 5px">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                @if (Session::get('role_name') === 'Super Admin')
                                                <a class="btn btn-sm bg-danger-light menuCat_delete" data-bs-toggle="modal" data-bs-target="#deleteMenuCat_{{$list->id}}">
                                                    <i class="feather-trash-2 me-1"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $num++
                                    @endphp
                                    {{-- model user delete --}}
                                        <div class="modal fade contentmodal" id="deleteMenuCat_{{$list->id}}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content doctor-profile">
                                                    <div class="modal-header pb-0 border-bottom-0  justify-content-end">
                                                        <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close"><i
                                                            class="feather-x-circle"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('menu/category/delete') }}" method="POST">
                                                            @csrf
                                                            <div class="delete-wrap text-center">
                                                                <div class="del-icon">
                                                                    <i class="feather-x-circle"></i>
                                                                </div>
                                                                <input type="hidden" name="id" class="e_menuCat_id" value="{{$list->id}}">
                                                                <h2>Sure you want to delete</h2>
                                                                <div class="submit-section">
                                                                    <button type="submit" class="btn btn-success me-2">Yes</button>
                                                                    <a class="btn btn-danger" data-bs-dismiss="modal">No</a>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
