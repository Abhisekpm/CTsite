<?php

namespace Database\Seeders;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::where('email', 'admin@admin.com')->first();

        $dt = Carbon::now();

        if (is_null($admin)) {
            $admin           = new Admin();
            $admin->name     = "Super Admin";
            $admin->email    = "admin@admin.com";
            $admin->role_name = "Super Admin";
            $admin->avtar = "photo_defaults.jpg";
            $admin->join_date = $dt;
            $admin->password = Hash::make('12345678');
            $admin->save();
        }
    }
}
