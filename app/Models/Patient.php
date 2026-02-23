<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name', 'date_of_birth', 'gender', 'phone', 'email',
        'address', 'blood_group', 'medical_history',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}
