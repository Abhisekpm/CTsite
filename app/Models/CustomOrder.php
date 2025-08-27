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
        'cake_sponge',
        'eggs_ok',
        'message_on_cake',
        'custom_decoration',
        'decoration_image_path',
        'allergies',
        'sms_consent', // Added missing SMS consent field
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'pickup_date' => 'date',
        'pickup_time' => 'datetime:H:i:s',
        'eggs_ok' => 'boolean',
        'sms_consent' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the images for the custom order.
     */
    public function images()
    {
        return $this->hasMany(CustomOrderImage::class);
    }
}
