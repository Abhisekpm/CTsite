<?php

namespace App\Http\Controllers;

use App\Models\Backend\Settings;
use App\Models\Backend\Testimonial;
use Illuminate\Http\Request;

class MainHomeController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::all();
        $settings = Settings::get()->first();
        return view('index', ['testimonials' => $testimonials, 'settings' => $settings]);
    }
}
