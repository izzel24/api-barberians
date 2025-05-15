<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantOperatingHour extends Model
{
    use HasFactory;

    protected $fillable = ['merchant_id', 'day_of_week', 'open_time', 'close_time'];

    public function merchant()
    {
        return $this->belongsTo(merchants::class, 'merchant_id');
    }
}
