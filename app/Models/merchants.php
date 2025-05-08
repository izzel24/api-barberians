<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class merchants extends Model
{
    use HasFactory;

    public function photos()
        {
            return $this->hasMany(MerchantPhoto::class);
        }
    public function user()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

        protected $fillable = [
            'company_name',
            'company_address',
            'nik',
            'status',
            'user_id',
            'rejection_reason'
        ];
}
