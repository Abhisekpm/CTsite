<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Backend\Gallery;
use App\Models\Backend\Settings;
use App\Models\Backend\GallCatId;
use App\Models\Backend\Testimonial;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $settings = Settings::get()->first();
        if ($search) {
            $ccat = Gallery::query()->where('name', 'LIKE', "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%")->orderBy('order', 'asc')->paginate(16);
            if($ccat->count() < 1)
            {
                $ccat = GallCatId::query()->where('name', 'LIKE', "%{$search}%")->orderBy('order', 'asc')->paginate(16);
            }
            $allcat = GallCatId::orderBy('order', 'asc')->get();
        } else {
            $ccat = GallCatId::orderBy('order', 'asc')->get();
            $allcat = '';
        }
        $testimonials = Testimonial::all();
        return view('collection', ['testimonials' => $testimonials, 'ccat' => $ccat, 'settings' => $settings, 'search' => $search, 'allcat' => $allcat]);
    }
    public function show(Request $request, $slug)
    {
        $search = $request->input('search');
        $ccat = GallCatId::where('slug', $slug)->first();
        if ($search) {
            $collection = Gallery::query()->where('name', 'LIKE', "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%")->orderBy('created_at', 'desc')->paginate(16);
            $allcat = GallCatId::orderBy('order', 'asc')->get();
        } else {
            $collection = Gallery::where('gal_cat_id', $ccat->id)->orderBy('created_at', 'desc')->get();
            $allcat = '';
        }
        $settings = Settings::get()->first();
        $testimonials = Testimonial::all();
        return view('singlecollection', ['testimonials' => $testimonials, 'collection' => $collection, 'settings' => $settings, 'ccat' => $ccat, 'search' => $search, 'allcat' => $allcat]);
    }
}
