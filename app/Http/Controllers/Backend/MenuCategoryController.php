<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Backend\MenuCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $menu_cat = MenuCategory::orderBy('order','asc')->get();
        return view('admin.menu_category.index', ['menu_cat' => $menu_cat]);
    }

    public function create()
    {
        return view('admin.menu_category.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $request->validate([
                    'name' => 'required',
                    'slug' => 'required|unique:menu_category',
                    'order' => 'required|integer',
                ]);

                $menu_cat               = new MenuCategory();
                $menu_cat->name         = $request->name;
                $menu_cat->order        = $request->order;
                $menu_cat->slug         = $request->slug;
                $menu_cat->description  = $request->description;
                
            

                if(isset($request->image))
                {
                    $imageName = time() . '.' . $request->image->extension();
                    $request->image->move(public_path('assets/menu'), $imageName);
                    $menu_cat->image = $imageName;
                }

                $menu_cat->meta_title       = $request->meta_title;
                $menu_cat->meta_keywords    = $request->meta_keywords;
                $menu_cat->meta_description = $request->meta_description;
                $menu_cat->save();
            } else {
                Toastr::error('Category creation failed :)', 'Error');
            }

            DB::commit();
            Toastr::success('Category created successfully :)', 'Success');

            return redirect()->route('menu-category');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Category creation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function edit(int $id)
    {
        $menu_cat = MenuCategory::find($id);
        return view('admin.menu_category.edit', ['menu_cat' => $menu_cat]);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $request->validate([
                    'name' => 'required',
                    'slug' => 'required|unique:menu_category,slug,'.$id,
                    'order' => 'required|integer',
                ]);

                $menu_cat = MenuCategory::find($id);
                $menu_cat->name             = $request->name;
                $menu_cat->slug             = $request->slug;
                $menu_cat->order            = $request->order;
                $menu_cat->description      = $request->description;
                $menu_cat->meta_title       = $request->meta_title;
                $menu_cat->meta_keywords    = $request->meta_keywords;
                $menu_cat->meta_description = $request->meta_description;

                $image_name = $request->hidden_image;
                $image = $request->file('image');

                if ($image) {
                    
                    if($menu_cat->image && file_exists(public_path('assets/menu/' . $image_name))) {
                        unlink(public_path('assets/menu/' . $image_name));
                    }
                    $image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('assets/menu/'), $image_name);
                    
                }

                $menu_cat->image = $image_name;
                $menu_cat->update();
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
                MenuCategory::destroy($request->id);
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
