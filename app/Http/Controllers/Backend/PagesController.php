<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\Backend\Pages;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class PagesController extends Controller
{
    public function index()
    {
        $pages = Pages::all();
        return view('admin.pages.index', ['pages' => $pages]);
    }

    public function create()
    {
        $pages = Pages::all();
        return view('admin.pages.create', ['pages' => $pages]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $request->validate([
                    'title' => 'required',
                    'slug' => 'required|unique:pages',
                    'description' => 'required',
                ]);

                $pages = new Pages();
                $pages->title = $request->title;
                $pages->slug = $request->slug;
                $pages->description = $request->description;
                $pages->meta_title = $request->meta_title;
                $pages->meta_keywords = $request->meta_keywords;
                $pages->meta_description = $request->meta_description;
                $pages->status = 1;
                $pages->save();
            } else {
                Toastr::error('Page creation failed :)', 'Error');
            }

            DB::commit();
            Toastr::success('Page created successfully :)', 'Success');

            return redirect()->route('pages');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Page creation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $page = Pages::find($id);
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $request->validate([
                    'title' => 'required',
                    'slug' => 'required',
                    'description' => 'required',
                ]);

                $id                         = $request->id;
                $title                      = $request->title;
                $slug                       = $request->slug;
                $description                = $request->description;
                $meta_title                 = $request->meta_title;
                $meta_keywords              = $request->meta_keywords;
                $meta_description           = $request->meta_description;

                $update = [
                    'id'                    => $id,
                    'title'                 => $title,
                    'slug'                  => $slug,
                    'description'           => $description,
                    'meta_title'            => $meta_title,
                    'meta_keywords'         => $meta_keywords,
                    'meta_description'      => $meta_description,
                ];

                Pages::where('id', $request->id)->update($update);
            } else {
                Toastr::error('Page updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Page updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Page updation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Super Admin') {
                Pages::destroy($request->id);
            } else {
                Toastr::error('Page deletion fail :)', 'Error');
            }

            DB::commit();
            Toastr::success('Page deleted successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Page deletion fail :)', 'Error');
            return redirect()->back();
        }
    }
}
