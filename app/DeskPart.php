<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskPart extends Model {

    use HasFactory;

    protected $table = 'deskparts';
    protected $fillable = ['title', 'desk_id'];

    public function desk() {
        return $this->belongsTo(Desk::class);
    }

    public function items() {
        return $this->hasMany(Item::class, 'deskpart_id');
    }
}
