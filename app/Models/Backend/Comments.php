<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'status',
        'website',
        'comment',
    ];

    protected $table = 'comments';
}
