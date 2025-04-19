<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Backend\GallCatId;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class GallCatController extends Controller
{
    public function index()
    {
        $gall_cat = GallCatId::orderBy('order','asc')->get();
        return view('admin.gallery_category.index', ['gall_cat' => $gall_cat]);
    }

    public function create()
    {
        return view('admin.gallery_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      =>      'required',
            'slug'      =>      'required|unique:gallery_category',
            'image'     =>      'required',
            'order'     =>      'required|unique:gallery_category',
        ]);

        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $ccat = new GallCatId();
                $ccat->name = $request->name;
                $ccat->slug = $request->slug;
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(base_path('assets/gallery'), $imageName);
                $ccat->image = $imageName;
                $ccat->order = $request->order;
                $ccat->meta_title = $request->meta_title;
                $ccat->meta_keywords = $request->meta_keywords;
                $ccat->meta_description = $request->meta_description;
                $ccat->save();
            } else {
                Toastr::error('Category creation failed :)', 'Error');
            }

            DB::commit();
            Toastr::success('Category created successfully :)', 'Success');

            return redirect()->route('ccat');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Category creation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $ccat = GallCatId::find($id);
        return view('admin.gallery_category.edit', ['ccat' => $ccat]);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {

                $ccat = GallCatId::find($id);
                $ccat->name               = $request->name;
                $ccat->slug           = $request->slug;
                $ccat->order           = $request->order;
                $ccat->meta_title              = $request->meta_title;
                $ccat->meta_keywords              = $request->meta_keywords;
                $ccat->meta_description              = $request->meta_description;



                $image_name = $request->hidden_image;
                $image = $request->file('image');

                if($image) {
                    unlink('assets/gallery/'.$image_name);
                    $image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(base_path('assets/gallery/'), $image_name);
                    $ccat->image = $image_name;
                }
                $ccat->update();
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
        dd($request->id);
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Super Admin') {
                GallCatId::destroy($request->id);
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
