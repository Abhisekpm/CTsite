<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrderImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'custom_order_id',
        'path',
    ];

    /**
     * Get the order that owns the image.
     */
    public function customOrder()
    {
        return $this->belongsTo(CustomOrder::class);
    }
}
