<?php

namespace App\Http\Controllers\Backend;

// use App\Models\r;
use Illuminate\Http\Request;
use App\Models\Backend\Settings;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Settings::where('id',1)->first();
        return view('admin.setting.settings', ['settings' => $settings]);
    }

    public function updatebasic(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $setting                    = Settings::find($id);
                $setting->website_name      = $request->website_name;

                $logo_name                  = $request->hidden_logo;
                $logo                       = $request->file('logo');

                $favicon_name               = $request->hidden_favicon;
                $favicon                    = $request->file('favicon');
                

                if($logo != '') {
                    unlink('assets/img/settings/'.$logo_name);

                    $logo_name = rand() . '.' . $logo->getClientOriginalExtension();
                    $logo->move(public_path('assets/img/settings/'), $logo_name);

                    $setting->logo = $logo_name;
                }
                if($favicon) {
                    // unlink('assets/img/settings/'.$favicon_name);
                    $favicon_name = rand() . '.' . $favicon->getClientOriginalExtension();
                    $favicon->move(public_path('assets/img/settings/'), $favicon_name);

                    $setting->favicon = $favicon_name;
                }
                $setting->update();


            } else {
                Toastr::error('Setting updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Setting updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Setting updation failed :)', 'Error');
            return redirect()->back();
        }
    }

    public function updateseo(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $setting                    = Settings::find($id);
                $setting->meta_title        = $request->meta_title;
                $setting->meta_keywords     = $request->meta_keywords;
                $setting->meta_description  = $request->meta_description;
                $setting->update();


            } else {
                Toastr::error('Setting updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Setting updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Setting updation failed :)', 'Error');
            return redirect()->back();
        }
    }
    
    public function contact()
    {
        $settings = Settings::where('id',1)->first();
        return view('admin.setting.contactsetting', ['settings' => $settings]);
    }
    
    public function updatecontact(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            if (Session::get('role_name') === 'Admin' || Session::get('role_name') === 'Super Admin') {
                $setting                    = Settings::find($id);
                
                $setting->contact_email     = $request->contact_email;
                $setting->contact_number    = $request->contact_number;
                $setting->update();


            } else {
                Toastr::error('Setting updation failed :)', 'Error');
            }
            DB::commit();
            Toastr::success('Setting updated successfully :)', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Setting updation failed :)', 'Error');
            return redirect()->back();
        }
    }
}
