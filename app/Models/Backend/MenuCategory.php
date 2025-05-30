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
        'order',
        'image',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    /**
     * Get the menu items for the category.
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'menu_category_id');
    }
}
