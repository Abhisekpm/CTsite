@extends('admin.layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">All Comments</h3>
                    <ul class="breadcrumb">
                        {{-- <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> --}}
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Comments</li>
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
                                    <h3 class="page-title">Comment List</h3>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                <thead class="student-thread">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Website</th>
                                        <th>Comment</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $num = 1
                                    @endphp
                                    @foreach ($comments as $key=>$list )
                                    <tr>
                                        <td>{{$num}}</td>
                                        <input type="hidden" class="comment_id" value="{{$list->id}}">
                                        <td>{{ $list->name }}</td>
                                        <td>{{ $list->email }}</td>
                                        <td>{{ $list->website }}</td>
                                        <td>{{ $list->comment }}</td>
                                        <td>{{ \Carbon\Carbon::parse($list->created_at)->format('d F Y') }}</td>
                                        {{-- <td>
                                            <div class="edit-delete-btn">
                                                @if ($list->status === 'Active')
                                                <a class="text-success">{{ $list->status }}</a>
                                                @elseif ($list->status === 'Inactive')
                                                <a class="text-warning">{{ $list->status }}</a>
                                                @elseif ($list->status === 'Disable')
                                                <a class="text-danger" >{{ $list->status }}</a>
                                                @else
                                                @endif
                                            </div>
                                        </td> --}}
                                        <td class="text-end">
                                            <div class="actions">
                                                <form action="{{ url('admin/comment/update', [$list->id]) }}" method="post">
                                                    @csrf
                                                    <input type="hidden" value="{{$list->name}}" name="name" />
                                                    <input type="hidden" value="{{$list->email}}" name="email" />
                                                    <input type="hidden" value="{{$list->website}}" name="website" />
                                                    <input type="hidden" value="{{$list->comment}}" name="comment" />
                                                    <input type="hidden" value="{{$list->blog_id}}" name="blog_id" />
                                                    @if($list->status == 0)
                                                    <button type="submit" class="btn btn-sm bg-danger-light"  style="margin-right: 5px">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    @else
                                                    <button type="submit" class="btn btn-sm bg-danger-light"  style="margin-right: 5px">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                </form>
                                                @if (Session::get('role_name') === 'Super Admin')
                                                <a class="btn btn-sm bg-danger-light comment_delete" data-bs-toggle="modal" data-bs-target="#delete">
                                                    <i class="feather-trash-2 me-1"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $num++
                                    @endphp
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

{{-- model user delete --}}
<div class="modal fade contentmodal" id="delete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content doctor-profile">
            <div class="modal-header pb-0 border-bottom-0  justify-content-end">
                <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close"><i
                    class="feather-x-circle"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('comment/delete') }}" method="POST">
                    @csrf
                    <div class="delete-wrap text-center">
                        <div class="del-icon">
                            <i class="feather-x-circle"></i>
                        </div>
                        <input type="hidden" name="id" class="e_comment_id" value="">
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

@section('script')

{{-- delete js --}}
<script>
    $(document).on('click','.comment_delete',function()
    {
        var _this = $(this).parents('table');
        $('.e_comment_id').val(_this.find('.comment_id').val());
    });
</script>
@endsection

@endsection
