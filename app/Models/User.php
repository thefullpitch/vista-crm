<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'user_type', 'gender', 'dob',
        'address', 'state_id', 'city_id', 'pincode_id',
        'profile_photo', 'status', 'kyc_status', 'registration_date', 'last_login', 'wallet_balance',
        'aadhar_number', 'pan_number', 'bank_account_number', 'aadhar_file', 'pan_file', 'bank_passbook_file', 'invoice_sample_file',
        'is_aadhar_verified', 'is_pan_verified', 'is_bank_verified', 'is_invoice_verified', 'is_photo_verified'
    ];

    public function state() { return $this->belongsTo(State::class); }
    
    public function city() { return $this->belongsTo(City::class); }
    public function pincode()
    {
        return $this->belongsTo(Pincode::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
