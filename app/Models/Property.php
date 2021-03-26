<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'image',
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

    /**
     * Return tours of this property
     *
     * @return HasMany
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    /**
     * Return the sale of this property
     *
     * @return HasMany
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Return features of this property
     *
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }
}
