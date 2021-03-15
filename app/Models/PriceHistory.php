<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Illuminate\Database\Eloquent\Builder;

/**
 * Class PriceHistory
 * @package App\Models
 */
class PriceHistory extends Model
{
    use HasFactory, HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = [
        'property_id',
        'start_date',
        'amount',
        'end_date'
    ];

    /**
     * @var string
     */
    protected $table = 'price_history';

    /**
     * @var string[]
     */
    protected $primaryKey = ['property_id', 'start_date', 'amount'];

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

//    /**
//     * Set the keys for a save update query.
//     *
//     * @param Builder $query
//     * @return Builder
//     */
//    protected function setKeysForSaveQuery(Builder $query): Builder
//    {
//        $keys = $this->getKeyName();
//        if(!is_array($keys)){
//            return parent::setKeysForSaveQuery($query);
//        }
//
//        foreach($keys as $keyName){
//            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
//        }
//
//        return $query;
//    }
//
//    /**
//     * Get the primary key value for a save query.
//     *
//     * @param mixed $keyName
//     * @return mixed
//     */
//    protected function getKeyForSaveQuery($keyName = null): mixed
//    {
//        if(is_null($keyName)){
//            $keyName = $this->getKeyName();
//        }
//
//        if (isset($this->original[$keyName])) {
//            return $this->original[$keyName];
//        }
//
//        return $this->getAttribute($keyName);
//    }
}
