<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHeader extends Model
{
    use HasFactory;
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id'); 
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function shippingContactMech()
    {
        return $this->belongsTo(ContactMech::class, 'shipping_contact_meches_id');
    }

    public function billingContactMech()
    {
        return $this->belongsTo(ContactMech::class, 'billing_contact_meches_id');
    }

    
    protected $fillable = [
        'order_date',
        'customer_id',
        'shipping_contact_mech_id',
        'billing_contact_mech_id',
    ];
}