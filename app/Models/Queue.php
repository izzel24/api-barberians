<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'merchant_id',
    'service_id',
    'date',
    'time',
    'status',
    'total_price',
];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(merchants::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
