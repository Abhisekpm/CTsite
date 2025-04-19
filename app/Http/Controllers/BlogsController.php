<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Backend\Blogs;
use App\Models\Backend\Category;
use App\Models\Backend\Comments;
use App\Models\Backend\Settings;
use Illuminate\Support\Facades\DB;
use App\Models\Backend\Testimonial;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class BlogsController extends Controller
{
    public function index()
    {
        $settings = Settings::get()->first();
        $testimonials = Testimonial::all();
        $blogs = Blogs::paginate(12);
        return view('blogs', ['testimonials' => $testimonials, 'settings' => $settings, 'blogs' => $blogs]);
    }

    public function show($slug)
    {
        $blog = Blogs::where('slug', $slug)->first();
        $blogs = Blogs::orderBy('created_at', 'desc')->get();
        $next_blog = Blogs::where('id','>', $blog->id)->first();
        $previous_blog = Blogs::where('id','<', $blog->id)->first();
        $categories = Category::all();
        $comments = Comments::where('blog_id', $blog->id)->get();
        foreach($categories as $cat)
        {
            $post_count = Blogs::where('category',$cat->id)->count();
        }

        if($previous_blog == null)
        {
            $previous_blog = Blogs::where('id','>', $blog->id)->where('id','!=',$next_blog->id)->first();
        }
        else if($next_blog == null)
        {
            $next_blog = Blogs::where('id','<', $blog->id)->where('id','!=',$previous_blog->id)->first();
        }
        $settings = Settings::get()->first();
        $testimonials = Testimonial::all();
        return view('singleblog', ['blog' => $blog, 'settings' => $settings, 'testimonials' => $testimonials, 'next_blog' => $next_blog, 'previous_blog' => $previous_blog, 'blogs' => $blogs, 'comments' => $comments]);
    }

    public function comment(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $request->validate([
                    'name' => 'required',
                    'email' => 'required',
                    'comment' => 'required',
                ]);

                $comment = new Comments();
                $comment->name = $request->name;
                $comment->email = $request->email;
                $comment->website = $request->website;
                $comment->comment = $request->comment;
                $comment->blog_id = $id;
                $comment->status = 0;

                $comment->save();
            } else {

                return redirect()->back()->with('error', 'Something Wrong !!');
            }

            DB::commit();
            return redirect()->back()->with('message', 'Your Comment has been added and in review. !!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something Wrong !!');
        }
    }
}
