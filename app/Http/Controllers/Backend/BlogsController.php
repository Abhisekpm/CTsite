<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\Backend\Blogs;
use App\Models\Backend\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Backend\Comments;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class BlogsController extends Controller
{
    public function index()
    {
        $blogs = Blogs::all();
        $categories = Category::all();
        return view('admin.blogs.index', ['blogs' => $blogs, 'categories' => $categories]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.blogs.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:blogs',
            'category' => 'required',
        ]);

        $blogs = new Blogs();
        $blogs->title = $request->title;
        $blogs->slug = $request->slug;
        $blogs->description = $request->description;

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(base_path('assets/blog_img'), $imageName);
        $blogs->image = $imageName;

        $blogs->category = $request->category;
        $blogs->tags = $request->tags;
        // $blog_cat->image = $request->f_image;
        $blogs->meta_title = $request->meta_title;
        $blogs->meta_description = $request->meta_description;
        $blogs->meta_keywords = $request->meta_keywords;
        $blogs->save();


        return redirect()->route('blogs');
    }

    public function edit(Int $id)
    {
        $blog = Blogs::find($id);
        $categories = Category::all();
        return view('admin.blogs.edit', ['blog' => $blog, 'categories' => $categories]);
    }

    /** blogs Update */
    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {

                $blog = Blogs::find($id);
                $blog->title               = $request->title;
                $blog->slug           = $request->slug;
                $blog->description           = $request->description;
                $blog->tags =          $request->tags;
                $blog->category =          $request->category;
                $blog->meta_title              = $request->meta_title;
                $blog->meta_keywords              = $request->meta_keywords;
                $blog->meta_description              = $request->meta_description;



                $image_name = $request->hidden_image;
                $image = $request->file('image');

                if ($image) {
                    unlink('assets/blog_img/' . $image_name);
                    $image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(base_path('assets/blog_img/'), $image_name);
                }
                $blog->image = $image_name;
                $blog->update();
            } else {
                Toastr::error('Blog updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Blog updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Blog updation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Super Admin') {
                Blogs::destroy($request->id);
            } else {
                Toastr::error('Blog deletion fail :)', 'Error');
            }

            DB::commit();
            Toastr::success('Blog deleted successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Blog deletion fail :)', 'Error');
            return redirect()->back();
        }
    }

    public function comments()
    {
        $comments = Comments::all();
        return view('admin.comments.index', ['comments' => $comments]);
    }

    public function commentDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Super Admin') {
                Comments::destroy($request->id);
            } else {
                Toastr::error('Comment deletion fail :)', 'Error');
            }

            DB::commit();
            Toastr::success('Comment deleted successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Comment deletion fail :)', 'Error');
            return redirect()->back();
        }
    }

    public function commentUpdate(int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $comments = Comments::find($id);
                if($comments->status == 0) {
                    DB::table('comments')
                      ->where('id', $id)
                      ->update(['status' => 1]);
                }
                elseif($comments->status == 1)
                {
                    DB::table('comments')
                      ->where('id', $id)
                      ->update(['status' => 0]);
                }
            } else {
                Toastr::error('Status updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Status updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Status updation failed :)', 'Error');
            return redirect()->back();
        }
    }
}
