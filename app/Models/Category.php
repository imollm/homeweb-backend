<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class Category
 * @package App\Models
 */
class Category extends Pivot
{
    /**
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * This field is to say to Eloquent the name of table
     * related of this model
     *
     * @var string
     */
    public $table = 'categories';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * Return all properties that have this category
     *
     * @return HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
