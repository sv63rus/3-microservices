<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['delivery_address', 'products'];

    protected $casts = [
        'products' => 'array', // Преобразование JSON в массив
    ];
}
