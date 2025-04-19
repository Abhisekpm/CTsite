<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'tags',
        'category',
        'meta_title',
        'meta_description',
        'meta_tags',
    ];

    protected $table = 'blogs';
}
