@extends('frontend.layout.main')
@section('title', 'Main Title')
@section('meta_keywords', '')
@section('meta_description', '')
@section('image', asset(''))
@section('main')
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center mt-5 pt-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">{{$blog->title}}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item text-white" aria-current="page"><a href="{{ url('blogs') }}">Blogs</a>
                    </li>
                    <li class="breadcrumb-item text-white active" aria-current="page"><a href="{{ url('blogs/'.$blog->slug) }}">{{$blog->title}}</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-xxl py-5">
        <div class="container blog">
            <div class="row g-10">
                <div class="col-lg-8">
                    <div class="blog-title">
                        <span>
                            <div class="blog-author d-flex align-items-center">
                                <img src="{{ asset('assets/img/settings/favicon.png') }}" width="50" height="50" class="pr-10" />
                                <span>
                                    <h1>{{$blog->title}}</h1>
                                    <p>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('F d, Y')}}</p>
                                </span>
                            </div>
                        </span>
                    </div>
                    <div class="blog-content">
                        <div class="blog-image">
                            <img src="{{ asset('assets/blog_img/'.$blog->image) }}" width="100%" />
                        </div>
                        <div class="blog-description mt-20">
                            {!! $blog->description !!}
                        </div>
                        <div class="blog-social">
                            <h6 class="pr-5">Share: </h6>
                            <div class="d-flex">
                                <a href="https://facebook.com/sharer/sharer.php?u={{url($blog->slug)}}/" target="_target"><i class="fab fa-facebook pr-15"></i></a>
                                <a href="https://twitter.com/intent/tweet/?text={{$blog->title}}&url={{url($blog->slug)}}/" target="_target"><i class="fab fa-twitter pr-15"></i></a>
                                <a href="mailto:?subject={{$blog->title}}&body={{url($blog->slug)}}/"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-30">
                        <div class="col-md-6 col-12">
                            <div class="blog-previous">
                                <div class="previous-heading">
                                    <h3>Previous Blog</h3>
                                </div>
                                <h6><a href="{{url('blogs/'.$previous_blog->slug)}}">{{$previous_blog->title}}</a></h6>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="blog-next text-right">
                                <div class="next-heading">
                                    <h3>Next Blog</h3>
                                </div>
                                <h6><a href="{{url('blogs/'.$next_blog->slug)}}">{{$next_blog->title}}</a></h6>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-30">
                        <h2 class="mb-30">Comments</h2>
                        @foreach ($comments as $comment)
                        <hr/>
                        <div class="blog-comments">
                            <div class="comment-heading">
                                <div class="heading-group d-flex justify-content-between align-items-center">
                                    <h5>{{$comment->name}} says:</h5>
                                    <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                </div>
                                <div class="comment-content">
                                    <p>{{$comment->comment}}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="row mt-30">
                        <div class="col-lg-12">
                            <div class="blog-comment bg-light p-20">
                                <h3>Add Comment</h3>
                                <form style="line-height: 3" action="{{url('comment/'.$blog->id)}}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col">
                                                <label>Name <span class="star-red">*</span></label>
                                                <input type="text" class="form-control" name="name" placeholder="Enter name">
                                            </div>
                                            <div class="col">
                                                <label>Email <span class="star-red">*</span></label>
                                                <input type="email" class="form-control" name="email" placeholder="Enter email">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col">
                                                <label>Website</label>
                                                <input type="url" class="form-control" name="website" placeholder="Enter website">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col">
                                                <label>Comment <span class="star-red">*</span></label>
                                                <textarea class="form-control" name="comment"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-30 mb-20">Submit</button>
                                    @if(session()->has('message'))
                                        <div class="alert alert-success">
                                            {{ session()->get('message') }}
                                        </div>
                                    @endif
                                    @if(session()->has('error'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('error') }}
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-lg-4 sidebar-image">
                    <div>
                        <img src="{{asset('assets/sidebar/download.jpg')}}" width="100%">
                    </div>
                    <div class="recent-blogs bg-light mt-30 p-20 br-10">
                        <div class="recent-content">
                            <h3>Recent Blogs</h3>
                        </div>
                        @foreach ($blogs as $blog)
                        <hr/>
                            <div class="d-flex align-items-center mb-10">
                                <img src="{{ asset('assets/blog_img/'.$blog->image) }}" class="rounded-circle mr-15" width="50" height="50">
                                <a href="{{url('blogs/'.$blog->slug)}}"><h6>{{$blog->title}}</h6></a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
