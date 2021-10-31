<?php

namespace App\Models;

use Carbon\Carbon;
use Hash;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, HasApiTokens;

    public $table = 'users';

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'FullName',
        'PreferredName',
        'email',
        'password',
        'IsSalesperson',
        'RepCode',
        'IsCustomer',
        'PhoneNumber',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
