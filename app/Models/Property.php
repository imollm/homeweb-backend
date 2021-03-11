<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Property
 * @package App\Models
 */
class Property extends Model
{
    use HasFactory;

    /**
     * Auto fillable fields of this model
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'user_id',
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
     * Auto fill timestamps
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Return owner of this property
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
