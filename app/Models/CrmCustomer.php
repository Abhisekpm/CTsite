<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'buyer_name', 'primary_phone', 'primary_email',
        'first_order', 'last_order', 'orders_count', 'fav_flavors',
        'eggs_ok', 'allergens', 'marketing_opt_in', 'channel_preference', 'notes'
    ];

    protected $casts = [
        'first_order' => 'date',
        'last_order' => 'date',
        'marketing_opt_in' => 'boolean',
    ];

    public function occasions()
    {
        return $this->hasMany(CrmOccasion::class, 'customer_id', 'customer_id');
    }

    public function customOrders()
    {
        return $this->hasMany(CustomOrder::class, 'email', 'primary_email');
    }

    public function scopeHighValue($query)
    {
        return $query->where('orders_count', '>=', 5);
    }

    public function scopeRecentlyActive($query)
    {
        return $query->where('last_order', '>=', now()->subMonths(6));
    }

    public function scopeHasAllergies($query)
    {
        return $query->whereNotNull('allergens')->where('allergens', '!=', '');
    }
}
