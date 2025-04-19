<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;

    protected $table = 'menu_category';

    protected $fillable = [

        'name',
        'slug',
        'image',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_description',

    ];
}
