<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    
    // Hide these fields from the serialized object
    protected $hidden = ['password'];
}