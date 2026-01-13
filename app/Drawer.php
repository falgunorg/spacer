<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drawer extends Model {

    protected $fillable = ['title', 'cabinet_id'];

    public function cabinet() {
        return $this->belongsTo(Cabinet::class);
    }

    public function items() {
        return $this->hasMany(Item::class);
    }
}
