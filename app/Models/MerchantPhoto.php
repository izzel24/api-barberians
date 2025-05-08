<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'path',
    ];

    public function merchant()
    {
        return $this->belongsTo(merchants::class);
    }
}
