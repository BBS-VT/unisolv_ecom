<?php

namespace App\Models;

use Carbon\Carbon;
use Hash;
use App\Traits\CompanyUserTrait;
use App\Traits\UUIDTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, HasApiTokens, UUIDTrait, CompanyUserTrait;

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
        'customer_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    /**
     * Define Relation with UserSetting Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    /**
     * Get User Specified setting
     *
     * @param string $key
     *
     * @return mixed
     */
    /**public function getSetting($key)
    {
        return UserSetting::getSetting($key, $this->id);
    }
    */

    /**
     * Set User Specified setting
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    /*public function setSetting($key, $value)
    {
        return UserSetting::setSetting($key, $value, $this->id);
    }*/

    /**
     * Get Full Name Attribute
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Define MediaCollection to SingleFile
     *
     * @return void
     */
    public function registerMediaCollections()
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    /**
     * Return Default User Avatar Url
     *
     * @return string (url)
     */
    public function getDefaultAvatar()
    {
        return asset('/images/users/user-1.jpg');
    }

    /**
     * Get User's Avatar Url || Default Avatar
     *
     * @return string (url)
     */
    public function getAvatarAttribute()
    {
        //$avatar = $this->getSetting('avatar');
        //return $avatar ? asset($avatar) : $this->getDefaultAvatar();
        return asset('/images/users/user-1.jpg');
    }


}
