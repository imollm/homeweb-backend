<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Illuminate\Database\Eloquent\Builder;

/**
 * Class PriceHistorySeeder
 * @package App\Models
 */
class PriceHistory extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = [
        'hash_id',
        'property_id',
        'start',
        'amount',
        'end'
    ];

    /**
     * @var string
     */
    protected $table = 'price_history';

    /**
     * @var string[]
     */
    protected $primaryKey = ['property_id', 'start', 'amount'];

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * Return property of this price history
     *
     * @return BelongsTo
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
