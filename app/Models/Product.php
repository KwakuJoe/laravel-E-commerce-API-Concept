<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        "store_id",
        "category_id",
        "user_id",
        "name",
        'description',
        'price',
    ] ;

        // protected $casts = [
    //     "is_done" => "boolean"
    // ];

    // relation between product and it images
    public function images(): HasMany
    {
        return $this->hasMany(ProductImages::class);
    }

    // inverse of relation user -products
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // inverse relationshp between order_item and product
    public function order_item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

}
