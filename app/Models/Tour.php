<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Tour
 * @package App\Models
 */
class Tour extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = [
        'property_id',
        'customer_id',
        'employee_id',
        'date',
        'time'
    ];

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $primaryKey = ['property_id', 'customer_id', 'employee_id', 'date', 'time'];

    /**
     * Return the employee of this tour
     *
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * Return the customer of this tour
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    /**
     * Return the property of this tour
     *
     * @return BelongsTo
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
