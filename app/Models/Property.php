<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Property
 * @package App\Models
 */
class Property extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * Auto fillable fields of this model
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'user_id',
        'city_id',
        'title',
        'reference',
        'plot_meters',
        'built_meters',
        'address',
        'longitude',
        'latitude',
        'description',
        'energetic_certification',
        'sold',
        'active',
        'price',
    ];

    /**
     * Return owner of this property
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Return the category of this property
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Return the city of this property
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Return price history of this property
     *
     * @return HasMany
     */
    public function priceHistory(): hasMany
    {
        return $this->hasMany(PriceHistory::class);
    }
}
