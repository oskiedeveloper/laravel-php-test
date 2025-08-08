<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_id',
        'title',
        'description',
        'location',
        'date',
        'status',
        'notes',
    ];

    /**
     * Get the user that owns the appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the patient associated with the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
