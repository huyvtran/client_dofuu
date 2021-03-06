<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cartalyst\Sentinel\Users\EloquentUser;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Authenticatable implements AuthenticatableUserContract, AuthenticatableContract
{
    use Notifiable;


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table    = 'ec_users';
    protected $fillable = ['name', 'email', 'password', 'phone', 'birthday', 'gender', 'role_id', 'actived', 'free_ship', 'image', 'address', 'lat', 'lng'];
    protected $hidden   = ['password', 'remember_token', 'api_token'];



    public function hasAnyRole($roles) {
        if(is_array($roles)) {
            foreach($roles as $role) {
                if($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($role) {
        if($this->role()->where('name', $role)->first()) {
            return true;
        }
        return false;
    }
    
    public function scopeOfStore($query, $store_id) {
        return $query->where('ratingtable_id', $store_id)->where('ratingtable_type', 'store');
    }

    public function scopeActived($query) {
        return $query->where('actived', 1);
    }

    public function scopeBanned($query) {
        return $query->where('banned', 1);
    }

    public function scopeIsAdmin() {
        if($this->role()->admin()->first()) {
            return true;
        }
        return false; 
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function receivesBroadcastNotificationsOn()
    {
        return 'users.'.$this->id;
    }

    public function getActivedAttribute($value) {
        if($value) {
            return true;
        } 
        return false;

    }

    public function getFreeShipAttribute($value) {
        if($value) {
            return true;
        } 
        return false;
    }

    public function getBannedAttribute($value) {
        if($value) {
            return true;
        } 
        return false;
    }

    public function getGenderAttribute($value) {
        if($value) {
            return true;
        } 
        return false;

    }

    public function likes() {
        return $this->belongsToMany('App\Models\Store', 'ec_likes', 'user_id', 'likeable_id');
    }

    public function favoriteStores() {
        return $this->belongsToMany('App\Models\Store', 'ec_favorite_stores', 'user_id', 'store_id');
    }

    public function commentLikes() {
        return $this->belongsToMany('App\Models\Comment', 'ec_comments', 'user_id', 'commentable_id');
    }

    public function orders() {
        return $this->hasMany('App\Models\RegularOrder', 'user_id');
    }

    public function store() {
        return $this->hasOne('App\Models\Store', 'user_id');
    }

    public function role() {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }
    
    public function active() {
        return $this->hasOne('App\Models\Activation', 'user_id');
    }

    public function ratings() {
        return $this->belongsToMany('App\Models\RatingType', 'ec_ratings', 'user_id', 'rating_type_id')->withPivot(['id', 'value']);
    }
}
