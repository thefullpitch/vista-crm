<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedemptionRequest extends Model
{
    protected $fillable = [
        'user_id', 'points_redeemed', 'status', 'shipping_address', 'remarks', 'processed_by', 'processed_at'
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function processor() { return $this->belongsTo(\App\Models\Admin::class, 'processed_by'); }
}
