<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GallCatId extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'order',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    protected $table = 'gallery_category';
}
