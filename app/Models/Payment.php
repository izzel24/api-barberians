<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_id',
        'method',
        'status',
        'transaction_id',
        'snap_token',
    ];

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }
}
