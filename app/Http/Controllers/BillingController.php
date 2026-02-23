<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $billings = Billing::with(['patient', 'appointment'])
            ->orderByDesc('billing_date')
            ->paginate(15);
        return view('billings.index', compact('billings'));
    }

    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        $appointments = Appointment::with('patient')->orderByDesc('appointment_date')->get();
        return view('billings.create', compact('patients', 'appointments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:pending,paid,cancelled',
            'description'    => 'nullable|string',
            'billing_date'   => 'required|date',
        ]);

        Billing::create($validated);

        return redirect()->route('billings.index')->with('success', 'Billing record created successfully.');
    }

    public function show(Billing $billing)
    {
        $billing->load(['patient', 'appointment.doctor']);
        return view('billings.show', compact('billing'));
    }

    public function edit(Billing $billing)
    {
        $patients = Patient::orderBy('name')->get();
        $appointments = Appointment::with('patient')->orderByDesc('appointment_date')->get();
        return view('billings.edit', compact('billing', 'patients', 'appointments'));
    }

    public function update(Request $request, Billing $billing)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:pending,paid,cancelled',
            'description'    => 'nullable|string',
            'billing_date'   => 'required|date',
        ]);

        $billing->update($validated);

        return redirect()->route('billings.index')->with('success', 'Billing record updated successfully.');
    }

    public function destroy(Billing $billing)
    {
        $billing->delete();
        return redirect()->route('billings.index')->with('success', 'Billing record deleted successfully.');
    }
}
