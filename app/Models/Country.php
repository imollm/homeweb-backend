<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 * @package App\Models
 */
class Country extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
      'code',
      'name',
    ];

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
