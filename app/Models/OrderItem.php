<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

        // inverse of relation order - order_items
        public function order(): BelongsTo
        {
            return $this->belongsTo(Order::class);
        }


        // relationship between order_item and products

        public function product(): BelongsTo
        {
            return $this->belongsTo(Product::class);
        }
}
