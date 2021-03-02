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
        'reference', 'plot_meters', 'build_meters', 'address', 'location', 'description', 'energetic_certification', 'active'
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
     * Return all customers interested on a property
     *
     * @return BelongsToMany
     */
    public function interested(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
