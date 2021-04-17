<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class Category
 * @package App\Models
 */
class Category extends Pivot
{
    use HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'image'];

    /**
     * This field is to say to Eloquent the name of table
     * related of this model
     *
     * @var string
     */
    public $table = 'categories';

    /**
     * Return all properties that have this category
     *
     * @return HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'category_id', 'id');
    }

    /**
     * Return for every category how many properties have related
     *
     * @return HasMany
     */
    public function propertiesCount(): HasMany
    {
        return $this->properties()
            ->selectRaw('category_id, count(*) as aggregate')
            ->groupBy('category_id');
    }
}
