<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id', 'amount', 'method', 'payment_date'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
} 