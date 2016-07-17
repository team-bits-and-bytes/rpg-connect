<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    
    // Hide these fields from the serialized object
    protected $hidden = ['password'];
    
    // When setting the password attribute, hash the value using BCRYPT
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }
    
    // Return the default avatar if no avatar exists
    public function getAvatarAttribute($value) {
        if ($value == null) {
            return '/images/default_avatar.jpg';
        } else {
            return $value;
        }
    }
}