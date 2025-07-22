<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id', 'doctor_id', 'notes'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    public function appointment()
    {
        return $this->belongsTo(\App\Models\Appointment::class);
    }
}
