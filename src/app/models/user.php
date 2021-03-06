<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    
    protected $append = ['rooms'];
    
    // Hide these fields from the serialized object
    protected $hidden = ['password', 'created_at', 'updated_at'];
    
    public function note() {
        return $this->hasOne('Models\Note');
    }
    
    // One-To-Many relationship with rooms
    public function getRoomsAttribute() {
        return Member::where('user_id', $this->attributes['id'])
            ->with('room')
            ->orderBy('favourite', 'desc')
            ->get()
            ->map(function($item) {
                return $item->room;
            });
    }
    
    // When setting the password attribute, hash the value using BCRYPT
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
    }
    
    // Return the default avatar if no avatar exists
    public function getAvatarAttribute($value) {
        if ($value == null) {
            return '/images/profile.png';
        } else {
            return $value;
        }
    }
}