<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'name',
        'option_1',
        'option_2',
        'price_1_heading',
        'price_1',
        'price_2_heading',
        'price_2',
        'price_3_heading',
        'price_3',
        'description',
    ];
}
