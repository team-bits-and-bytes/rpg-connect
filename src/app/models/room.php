<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model {
    protected $table = 'rooms';
    
    // Serialize this custom attribute
    protected $appends = ['private', 'is_member'];
    
    // Hide these fields from the serialized object
    protected $hidden = ['password', 'updated_at'];

    // owner of the room
    public function owner() {
        return $this->belongsTo('Models\User', 'owner_id');
    }
    
    // One-To-Many relationship with members
    public function members() {
        return $this->hasMany('Models\Member');
    }
    
    public function getPrivateAttribute() {
        return $this->attributes['password'] != null;
    }
    
    // "Hackish" way to detect if current user is a member of the room
    public function getIsMemberAttribute() {
        return $this->members()->get()->contains('user_id', $_SESSION['auth_user']);
    }
    
    // When setting the password attribute, hash the value using BCRYPT
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }
}