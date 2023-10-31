<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetTokens extends Model
{
    use HasFactory;

    public $fillable = [
        'email',
        'token',
        'created_at'
    ] ;

    public $table = 'password_reset_tokens';
    public $timestamps = false;

    protected $primaryKey = 'email';
}
