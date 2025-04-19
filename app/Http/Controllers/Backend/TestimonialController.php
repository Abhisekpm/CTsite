<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Backend\Testimonial;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
        return view('admin.testimonials.index', ['testimonials' => $testimonials]);
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function edit(int $id)
    {
        $testi = Testimonial::find($id);
        return view('admin.testimonials.edit', ['testi' => $testi]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $testi = new Testimonial();
                $testi->name = $request->name;
                // $testi->position = $request->position;
                if(isset($request->image)) {
                $imageName = time().'.'.$request->image->extension();
                $request->image->move(public_path('assets/testimonials'), $imageName);
                $testi->image = $imageName;
                }
                $testi->quote = $request->quote;
                $testi->status = 1;
                $testi->save();
            } else {
                Toastr::error('Testimonial creation failed :)', 'Error');
            }

            DB::commit();
            Toastr::success('Testimonial created successfully :)', 'Success');

            return redirect()->route('testimonials');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Testimonial creation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $name               = $request->name;
                // $position           = $request->position;
                $quote              = $request->quote;

                $image_name = $request->hidden_image;
                $image = $request->file('image');

                if($image) {
                    unlink('assets/testimonials/'.$image_name);
                    $image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('assets/testimonials/'), $image_name);
                }

                $update = [
                    'name'          => $name,
                    // 'position'      => $position,
                    'image'         => $image_name,
                    'quote'         => $quote,
                    'status'        => 1,
                ];

                Testimonial::where('id', $request->id)->update($update);
            } else {
                Toastr::error('Testimonial updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Testimonial updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Testimonial updation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Super Admin') {
                Testimonial::destroy($request->id);
            } else {
                Toastr::error('Testimonial deletion fail :)', 'Error');
            }

            DB::commit();
            Toastr::success('Testimonial deleted successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Testimonial deletion fail :)', 'Error');
            return redirect()->back();
        }
    }
}
