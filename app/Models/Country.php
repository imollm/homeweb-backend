<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 * @package App\Models
 */
class Country extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = [
      'code',
      'name',
    ];

    /**
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
