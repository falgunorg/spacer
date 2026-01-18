<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model {

    protected $fillable = ['name', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cabinets() {
        return $this->hasMany(Cabinet::class, 'location_id');
    }

    public function items() {
        return $this->hasMany(Item::class, 'location_id');
    }
}
