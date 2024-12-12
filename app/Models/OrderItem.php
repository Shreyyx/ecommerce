<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    
    protected $fillable = [
        'order_id',     
        'product_id',   
        'quantity',     
        'status'        
    ];

    protected $primaryKey = 'order_item_seq_id';  
    public $incrementing = false; 

    public function orderHeader()
    {
        return $this->belongsTo(OrderHeader::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'order_id')->onDelete('cascade');
}

    
}