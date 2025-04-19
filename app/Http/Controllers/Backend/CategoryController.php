<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\Backend\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Brian2694\Toastr\Facades\Toastr;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function edit(Int $id)
    {
        $category = Category::find($id);
        return view('admin.category.edit', ['category' => $category]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $request->validate([
                    'title' => 'required',
                    'slug' => 'required|unique:categories',
                    'description' => 'required',
                ]);

                $category = new Category();
                $category->title = $request->title;
                $category->slug = $request->slug;
                $category->description = $request->description;
                $category->save();
            } else {
                Toastr::error('Category creation failed :)', 'Error');
            }

            DB::commit();
            Toastr::success('Category created successfully :)', 'Success');

            return redirect()->route('category');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Category creation failed :)', 'Error');
            return redirect()->back();
        }
    }

    /** category Update */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $id                 = $request->id;
                $title              = $request->title;
                $slug               = $request->slug;
                $description        = $request->description;

                $update = [
                    'id'            => $id,
                    'title'         => $title,
                    'slug'          => $slug,
                    'description'   => $description,
                ];

                Category::where('id', $request->id)->update($update);
            } else {
                Toastr::error('Category updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Category updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Category updation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Super Admin') {
                Category::destroy($request->id);
            } else {
                Toastr::error('Category deletion fail :)', 'Error');
            }

            DB::commit();
            Toastr::success('Category deleted successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Category deletion fail :)', 'Error');
            return redirect()->back();
        }
    }
}
