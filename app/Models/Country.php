<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
      'longitude',
      'latitude'
    ];

    /**
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Return for every category how many properties have related
     *
     * @return HasMany
     */
    public function citiesCount(): HasMany
    {
        return $this->cities()
            ->selectRaw('country_id, count(*) as aggregate')
            ->groupBy('country_id');
    }

    /**
     * Return for every country, properties that have the cities of this country
     *
     * @return HasManyThrough
     */
    public function properties(): HasManyThrough
    {
        return $this->hasManyThrough(Property::class, City::class, 'country_id', 'city_id', 'id', 'id');
    }
}
