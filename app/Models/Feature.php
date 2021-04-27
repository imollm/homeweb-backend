<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Feature
 * @package App\Models
 */
class Feature extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Return the properties that have this feature
     *
     * @return BelongsToMany
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }

    /**
     * Relation with FeatureProperty pivot table
     *
     * @return HasMany
     */
    public function featuresProperties(): HasMany
    {
        return $this->hasMany(FeatureProperty::class);
    }

    /**
     * Return for every feature how many properties have this feature
     *
     * @return HasMany
     */
    public function propertiesCount(): HasMany
    {
        return $this->featuresProperties()
                    ->selectRaw('feature_id, count(*) as aggregate')
                    ->groupBy('feature_id');
    }
}
