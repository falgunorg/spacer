<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model {

    use HasFactory;

    protected $fillable = ['title', 'location_id', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // App\Cabinet.php
    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function deskparts() {
        return $this->hasMany(DeskPart::class, 'desk_id');
    }

    public function items() {
        return $this->hasMany(Item::class);
    }
}
