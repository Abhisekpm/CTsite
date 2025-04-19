<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Backend\Settings;
use App\Models\Backend\Testimonial;

class PagesController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::all();
        $settings = Settings::get()->first();
        return view('about', ['testimonials' => $testimonials, 'settings' => $settings]);
    }

    public function ourmenu()
    {
        $settings = Settings::get()->first();
        $testimonials = Testimonial::all();
        return view('ourmenu', ['testimonials' => $testimonials, 'settings' => $settings]);
    }

    public function contact()
    {
        $settings = Settings::get()->first();
        $testimonials = Testimonial::all();
        return view('contact', ['testimonials' => $testimonials, 'settings' => $settings]);
    }
    
    public function cakes()
    {
                $testimonials = Testimonial::all();
        $settings = Settings::get()->first();
        return view('cakes', ['testimonials' => $testimonials, 'settings' => $settings]);
    }
}
