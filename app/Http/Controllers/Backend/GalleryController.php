<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\Backend\Gallery;
use App\Models\Backend\GallCatId;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class GalleryController extends Controller
{
    public function index()
    {
        $ccat = GallCatId::all();
        $gallery = Gallery::orderBy('order','asc')->get();
        return view('admin.gallery.index', ['gallery' => $gallery, 'ccat' => $ccat]);
    }

    public function create()
    {
        $ccat = GallCatId::all();
        return view('admin.gallery.create', ['ccat' => $ccat]);
    }

    public function edit(int $id)
    {
        $collection = Gallery::find($id);
        $ccat = GallCatId::all();
        return view('admin.gallery.edit', ['collection' => $collection, 'ccat' => $ccat]);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                
                // $request->validate([
                //     'image'     =>      'required',
                //     'order'     =>      'required',
                // ]);

                $coll = Gallery::find($id);
                $coll->name               = $request->name;
                // $coll->order           = $request->order;
                $coll->description           = $request->description;
                $coll->gal_cat_id           = $request->gal_cat_id;
                $coll->meta_title              = $request->meta_title;
                $coll->meta_keywords              = $request->meta_keywords;
                $coll->meta_description              = $request->meta_description;



                $image_name = $request->hidden_image;
                $image = $request->file('image');

                if($image) {
                    unlink('assets/gallery/'.$image_name);
                    $image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(base_path('assets/gallery/'), $image_name);
                    $coll->image = $image_name;
                }
                $coll->update();
            } else {
                Toastr::error('Collection updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Collection updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Collection updation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'     =>      'required',
            // 'order'     =>      'required',
        ]);
        
        // dd($request->image);

        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $ccat = new Gallery();
                $ccat->name = $request->name;
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(base_path('assets/gallery'), $imageName);
                $ccat->image = $imageName;
                $ccat->gal_cat_id = $request->gal_cat_id;
                // $ccat->order = $request->order;
                $ccat->description = $request->description;
                $ccat->meta_title = $request->meta_title;
                $ccat->meta_keywords = $request->meta_keywords;
                $ccat->meta_description = $request->meta_description;
                $ccat->save();
            } else {
                Toastr::error('Collection creation failed :)', 'Error');
            }

            DB::commit();
            Toastr::success('Collection created successfully :)', 'Success');

            return redirect()->route('collection');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Collection creation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Super Admin') {
                Gallery::destroy($request->id);
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
