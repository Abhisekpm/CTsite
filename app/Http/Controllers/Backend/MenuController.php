<?php

namespace App\Http\Controllers\Backend;

use App\Models\Backend\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Backend\MenuCategory;
use App\Models\MenuOptions;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        $menu_cat = MenuCategory::all();
        return view('admin.menu.index', ['menus' => $menus, 'menu_cat' => $menu_cat]);
    }

    public function create()
    {
        $menu_cat = MenuCategory::all();
        return view('admin.menu.create', ['menu_cat' => $menu_cat]);
    }

    public function edit(int $id)
    {
        $menu = Menu::find($id);
        $menu_cat = MenuCategory::all();
        
        return view('admin.menu.edit', ['menu' => $menu, 'menu_cat' => $menu_cat]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $request->validate([
                    'name' => 'required',
                    'category' => 'required',
                ]);

                $option_1 = $request->option_1;
                $option_2 = $request->option_2;
                $price_1 = $request->price_1;
                $price_2 = $request->price_2;

                $menu                       = new Menu();
                $menu->name                 = $request->name;
                $menu->menu_category_id     = $request->category;
                $menu->description          = $request->description;
                $menu->save();

                // $id = $menu->id;

                // if(isset($option_1))
                // {
                //     for($i = 0;$i < count($option_1); $i++)
                //     {
                //         $menu_option = [
                //             'menu_id' => $id,
                //             'option_1' => $option_1[$i],
                //             'option_2' => $option_2[$i],
                //             'price_1' => $price_1[$i],
                //             'price_2' => $price_2[$i],
                //         ];
                //         // dd($menu_option);
                //         DB::table('menu_options')->insert($menu_option);
                //     }
                // }

            } else {
                Toastr::error('Menu creation failed :)', 'Error');
            }

            DB::commit();
            Toastr::success('Menu created successfully :)', 'Success');

            return redirect()->route('menu');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Menu creation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {

                $request->validate([
                    'name' => 'required',
                    'category' => 'required',
                ]);

                $option_1 = $request->option_1;
                $option_2 = $request->option_2;
                $price_1 = $request->price_1;
                $price_2 = $request->price_2;

                $menu                       = Menu::find($id);
                $menu->name                 = $request->name;
                $menu->menu_category_id     = $request->category;
                $menu->description          = $request->description;
                $menu->save();

                // if(isset($option_1))
                // {
                //     for($i = 0;$i < count($option_1); $i++)
                //     {
                //         $menu_option = [
                //             'option_1' => $option_1[$i],
                //             'option_2' => $option_2[$i],
                //             'price_1' => $price_1[$i],
                //             'price_2' => $price_2[$i],
                //         ];
                        
                //         DB::table('menu_options')->where('menu_id', $id)->updateOrInsert($menu_option);
                //     }
                // }

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
                Menu::destroy($request->id);

                MenuOptions::where('menu_id', $request->id)->delete();

            } else {
                Toastr::error('Menu deletion fail :)', 'Error');
            }

            DB::commit();
            Toastr::success('Menu deleted successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Menu deletion fail :)', 'Error');
            return redirect()->back();
        }
    }
}
