<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'plot_meters', 'build_meters', 'address', 'location', 'description'
    ];

    public $timestamps = true;
}
