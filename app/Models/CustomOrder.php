<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'email',
        'phone',
        'pickup_date',
        'pickup_time',
        'cake_size',
        'cake_flavor',
        'eggs_ok',
        'message_on_cake',
        'custom_decoration',
        'decoration_image_path',
        'allergies',
        'status', // Although it defaults, allow it to be fillable if needed later
        'price', // Added price
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'custom_orders'; // Explicitly defining, though Laravel convention would likely find it

    /**
     * Get the images for the custom order.
     */
    public function images()
    {
        return $this->hasMany(CustomOrderImage::class);
    }
}
