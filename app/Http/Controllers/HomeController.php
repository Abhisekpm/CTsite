<?php

namespace App\Http\Controllers;

use App\Models\Backend\Blogs;
use App\Models\Backend\Category;
use App\Models\Backend\GallCatId;
use App\Models\Backend\Gallery;
use App\Models\Backend\Pages;
use App\Models\Backend\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    /** home dashboard */
    public function index()
    {
        $blogs = Blogs::all();
        $categories = Category::all();
        $pages = Pages::all();
        $testimonials = Testimonial::all();
        $collection = Gallery::all();
        $ccat = GallCatId::all();
        return view('admin.dashboard.home', ['blogs' => $blogs, 'categories' => $categories, 'pages' => $pages, 'testimonials' => $testimonials, 'collection' => $collection, 'ccat' => $ccat]);
    }

    /** profile user */
    public function userProfile()
    {
        return view('admin.dashboard.profile');
    }

    /** teacher dashboard */
    public function teacherDashboardIndex()
    {
        return view('dashboard.teacher_dashboard');
    }

    /** student dashboard */
    public function studentDashboardIndex()
    {
        return view('dashboard.student_dashboard');
    }
}
