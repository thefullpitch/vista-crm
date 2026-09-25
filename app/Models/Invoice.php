<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'shop_id', 'invoice_number', 'invoice_date', 'amount', 'points_earned',
        'document_file', 'status', 'rejection_reason', 'verified_by', 'verified_at',
        'customer_name', 'customer_phone', 'product_name', 'verification_remark'
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function shop() { return $this->belongsTo(Shop::class); }
    public function verifier() { return $this->belongsTo(\App\Models\Admin::class, 'verified_by'); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
}
