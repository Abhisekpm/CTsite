<?php

namespace App\Models;

use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    public $fillable = ['name', 'email', 'subject', 'message'];

    protected $table = 'emails';

    /**
     * Write code on Method
     *
     * @return response()
     */
    public static function boot() {

        parent::boot();

        static::created(function ($item) {

            $adminEmail = "contact@dartdigitalagency.com";
            Mail::to($adminEmail)->send(new SendEmail($item));
        });
    }
}
