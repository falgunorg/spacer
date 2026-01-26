<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

    protected $fillable = ['name', 'user_id', 'item_type', 'description', 'image', 'qty', 'location_id', 'cabinet_id', 'drawer_id', 'trackable', 'desk_id', 'deskpart_id'];
    protected $hidden = ['created_at', 'updated_at'];

    protected static function booted() {
        static::creating(function ($item) {
            // Get the last ID or set to 0 if table is empty
            $lastItem = static::orderBy('id', 'desc')->first();
            $nextId = $lastItem ? $lastItem->id + 1 : 1;

            // str_pad(string, length, padding_string, type)
            $item->serial_number = str_pad($nextId, 5, '0', STR_PAD_LEFT);
        });
    }

    public function getShowPhotoAttribute() { // Changed from getImagePathAttribute
        if (!$this->image) {
            return asset('upload/no-image.png');
        }
        return asset('upload/items/' . $this->image);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function itemLocation() {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function cabinet() {
        return $this->belongsTo(Cabinet::class);
    }

    public function drawer() {
        return $this->belongsTo(Drawer::class);
    }

    public function itemType() {
        return $this->belongsTo(ItemType::class, 'item_type');
    }

    public function desk() {
        return $this->belongsTo(Desk::class);
    }

    public function deskpart() {
        return $this->belongsTo(DeskPart::class, 'deskpart_id');
    }
}
