<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model {

    use HasFactory;

    protected $fillable = ['name'];

    public function items() {
        return $this->hasMany(Item::class, 'item_type');
    }
}
