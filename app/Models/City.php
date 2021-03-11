<?php

namespace App\Models;

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
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'country_id',
    ];

    /**
     * @var bool
     */
    public $timestamps = true;

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
}
