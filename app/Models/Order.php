<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

   protected $fillable = [
    'order_id',
    'user_id',
    'order_date',
    'location',
    'name',
    'phone',
    'alternate_phone',
    'total_amount',
    'additional_information',
    'status'
   ];

   // relations between Order & Order Items
   public function order_items(): HasMany
   {
       return $this->hasMany(OrderItem::class);
   }

   // inverse relationship between user - orders
   public function user(): BelongsTo
   {
       return $this->belongsTo(User::class);
   }
}
