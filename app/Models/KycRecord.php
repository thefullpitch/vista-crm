<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycRecord extends Model
{
    protected $fillable = [
        'user_id', 'document_type', 'document_number', 'document_file_front', 
        'document_file_back', 'status', 'rejection_reason', 'verified_by', 'verified_at'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function verifier() { return $this->belongsTo(\App\Models\Admin::class, 'verified_by'); }
}
