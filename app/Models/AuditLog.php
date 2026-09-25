<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['admin_user_id', 'action', 'module', 'details', 'ip_address'];
    public function admin() { return $this->belongsTo(\App\Models\Admin::class, 'admin_user_id'); }
}
