<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'seller_id',
        'date',
        'amount',
        'hash_id'
    ];

    public $incrementing = false;

    protected $primaryKey = ['property_id', 'customer_id', 'employee_id', 'date'];

    /**
     * Return the employee of this tour
     *
     * @return BelongsTo
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    /**
     * Return the customer of this tour
     *
     * @return BelongsTo
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
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
