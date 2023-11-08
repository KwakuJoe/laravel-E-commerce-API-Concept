<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;



class ProductImages extends Model
{
    use HasFactory;

    protected $fillable = [
        "product_id",
        "file_path",
    ];

    protected $casts = [
        // 'order_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // inverse relation between product and it images

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    // boot function
    public static function booted():void
    {
        self::deleted(function (ProductImages $productImages){
            return Storage::delete($productImages->file_path);
        });
    }
}
