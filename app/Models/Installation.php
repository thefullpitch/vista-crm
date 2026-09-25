<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'customer_mobile', 'product_serial_number', 'amount',
        'installation_photo', 'invoice_number', 'invoice_amount', 'invoice_image', 'products_data',
        'points_earned', 'status', 'rejection_reason', 'verified_by', 'verified_at', 'verification_remark'
    ];

    protected $casts = [
        'installation_photo' => 'array',
        'products_data' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function verifier() { return $this->belongsTo(\App\Models\Admin::class, 'verified_by'); }
}
