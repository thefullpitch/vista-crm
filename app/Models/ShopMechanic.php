<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopMechanic extends Model
{
    protected $fillable = ['shop_id', 'mechanic_id', 'status'];
    public function shop() { return $this->belongsTo(Shop::class); }
    public function mechanic() { return $this->belongsTo(User::class, 'mechanic_id'); }
}
