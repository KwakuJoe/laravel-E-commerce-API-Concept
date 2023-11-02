<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;


    protected $fillable = [
        "user_id",
        "name",
        "address",
        'description',
        'is_verified',
    ] ;

    protected $casts = [
        "is_verified" => "boolean"
    ];
}
