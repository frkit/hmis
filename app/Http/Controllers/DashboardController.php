<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Medicine;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'patients' => Patient::count(),
            'doctors' => Doctor::count(),
            'appointments_today' => Appointment::whereDate('appointment_date', today())->count(),
            'pending_bills' => Billing::where('status', 'pending')->count(),
            'medicines_low_stock' => Medicine::where('stock', '<', 10)->count(),
        ];

        $recent_appointments = Appointment::with(['patient', 'doctor'])
            ->orderByDesc('appointment_date')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_appointments'));
    }
}
