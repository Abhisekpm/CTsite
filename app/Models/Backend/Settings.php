<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_name',
        'logo',
        'favicon',
        'contact_email',
        'contact_number',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $table = 'settings';
}
