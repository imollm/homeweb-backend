<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
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
    use HasFactory, HasTimestamps;

    /**
     * Auto fill this data on database
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'description'
    ];

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
