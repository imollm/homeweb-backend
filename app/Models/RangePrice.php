<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'range',
        'big_price',
        'small_price'
    ];

    public $timestamps = true;
}
