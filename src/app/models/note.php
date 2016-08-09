<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model {
    protected $table = 'notes';
    
    // Allow mass assignment on these attributes
    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo('Models\User');
    }
}