<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    
    // Hide these fields from the serialized object
    protected $hidden = ['password'];
    
    public static function boot() {
        parent::boot();
        
        // Encrypt the password when creating the record
        static::creating(function($user) {
            $user->password = password_hash($user->password, PASSWORD_BCRYPT);
        });
    }
}