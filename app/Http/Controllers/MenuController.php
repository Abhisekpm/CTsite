<?php

namespace App\Http\Controllers;

use App\Models\Backend\Menu;
use Illuminate\Http\Request;
use App\Models\Backend\Settings;
use App\Models\Backend\Testimonial;
use App\Models\Backend\MenuCategory;
use App\Models\MenuOptions;

class MenuController extends Controller
{
    public function category()
    {
        $testimonials = Testimonial::all();
        $settings = Settings::get()->first();
        $all_cat = MenuCategory::orderBy('order','asc')->get();
        return view('menu_category', ['all_cat' => $all_cat, 'testimonials' => $testimonials, 'settings' => $settings]);
    }

    public function show($slug)
    {
        $testimonials = Testimonial::all();
        $settings = Settings::get()->first();
        $menu_cat = MenuCategory::where('slug', $slug)->get()->first();

        $menus = Menu::where('menu_category_id', $menu_cat->id)->get();

        $menu_options= MenuOptions::all();
        return view('ourmenu', ['menu_cat' => $menu_cat, 'testimonials' => $testimonials, 'settings' => $settings, 'menus' => $menus, 'menu_options' => $menu_options]);
    }
}
