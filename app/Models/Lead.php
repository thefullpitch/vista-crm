<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'customer_mobile', 'product_interest', 
        'remarks', 'status', 'assigned_to', 'points_earned', 'awarded_progress_points', 'awarded_converted_points',
        'invoice_file', 'admin_remarks'
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function assignee() { return $this->belongsTo(\App\Models\Admin::class, 'assigned_to'); }
}
