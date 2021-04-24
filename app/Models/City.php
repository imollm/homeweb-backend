<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class City
 * @package App\Models
 */
class City extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'country_id',
        'latitude',
        'longitude'
    ];

    /**
     * Return the country where city is located
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Returns the properties located in this city
     *
     * @return HasMany
     */
    public function properties(): hasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Return for every category how many properties have related
     *
     * @return HasMany
     */
    public function propertiesCount(): HasMany
    {
        return $this->properties()
            ->selectRaw('city_id, count(*) as aggregate')
            ->groupBy('city_id');
    }
}
