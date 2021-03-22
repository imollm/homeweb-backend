<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
