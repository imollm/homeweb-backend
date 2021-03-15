<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Builder;

class PriceHistory extends Model
{
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'property_id',
        'start_date',
        'amount',
        'end_date'
    ];

    protected $table = 'price_history';

    protected $primaryKey = ['property_id', 'start_date', 'amount'];

    public $incrementing = false;

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
