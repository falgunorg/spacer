<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model {

    protected $fillable = ['title', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cabinets() {
        return $this->hasMany(Cabinet::class);
    }
}
