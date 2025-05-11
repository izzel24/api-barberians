<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class merchants extends Model
{
    use HasFactory;

    public function photos()
        {
            return $this->hasMany(MerchantPhoto::class, 'merchant_id');
        }
    public function user()
        {
            return $this->belongsTo(User::class, 'user_id');
        }
    public function services()
        {
            return $this->hasMany(Service::class, 'merchant_id');
        }
    public function operatingHours()
        {
            return $this->hasMany(MerchantOperatingHour::class, 'merchant_id');
        }

        protected $fillable = [
            'company_name',
            'company_address',
            'nik',
            'status',
            'city',
            'description',
            'user_id',
            'rejection_reason'
        ];
}
