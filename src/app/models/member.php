<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model {
    protected $table = 'members';
    
    // Hide these fields from the serialized object
    protected $hidden = ['updated_at'];
    
    // Allow mass assignment on these attributes
    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo('Models\User');
    }
    
    public function room() {
        return $this->belongsTo('Models\Room');
    }
}