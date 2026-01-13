<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemLog extends Model {

    protected $fillable = ['item_id', 'user_id', 'message'];

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
