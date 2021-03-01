<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Role
 * @package App\Models
 */
class Role extends Model
{
    use HasFactory;

    /**
     * Auto fill this data on database
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'description'
    ];

    /**
     * Active auto insert timestamps on database
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Return users by this role
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
