<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasTimestamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'fiscal_id',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Return properties owned by this user
     *
     * @return HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Return roles of this user
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Return tours of role employee
     *
     * @return HasMany
     */
    public function makeTours(): HasMany
    {
        return $this->hasMany(Tour::class, 'employee_id', 'id');
    }

    /**
     * Return tours of role customer
     *
     * @return HasMany
     */
    public function visitTours(): HasMany
    {
        return $this->hasMany(Tour::class, 'customer_id', 'id');
    }

    /**
     * Return the sales of one employee
     *
     * @return HasMany
     */
    public function mySales(): HasMany
    {
        return $this->hasMany(Sale::class, 'seller_id', 'id');
    }

    /**
     * Return the buys of one customer
     *
     * @return HasMany
     */
    public function myPurchases(): HasMany
    {
        return $this->hasMany(Sale::class, 'buyer_id', 'id');
    }

    /**
     * Tells if the user have admin role
     *
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->role->first()->name === 'admin';
    }
}
