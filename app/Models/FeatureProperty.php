<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class FeatureProperty
 * @package App\Models
 */
class FeatureProperty extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * @var string
     */
    protected $table = 'feature_property';

    /**
     * @var string[]
     */
    protected $fillable = [
        'feature_id',
        'property_id'
    ];

    /**
     * Return feature related
     *
     * @return BelongsTo
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Return property related
     *
     * @return BelongsTo
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
