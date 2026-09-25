<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name', 'owner_id', 'gst_number', 'contact_number',
        'address', 'state_id', 'city_id', 'pincode_id', 'status'
    ];
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function mechanics() { return $this->belongsToMany(User::class, 'shop_mechanics', 'shop_id', 'mechanic_id')->withPivot('status')->withTimestamps(); }
    public function state() { return $this->belongsTo(State::class); }
    
    public function city() { return $this->belongsTo(City::class); }
    public function pincode() { return $this->belongsTo(Pincode::class); }
    public function users() { return $this->belongsToMany(User::class); }
}
