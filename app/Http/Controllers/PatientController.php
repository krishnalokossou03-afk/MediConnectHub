<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::all();
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
        ]);

        Patient::create($validated);
        return redirect()->route('patients.index')->with('success', 'Patient ajouté avec succès.');
    }

    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
        ]);

        $patient->update($validated);
        return redirect()->route('patients.index')->with('success', 'Patient mis à jour.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient supprimé.');
    }

    public function dashboardPatient()
    {
        $user = Auth::user();
        $patient = $user->patient ?? null;
        $upcomingAppointments = collect();
        $recentPrescriptions = collect();
        $stats = [
            'appointments' => 0,
            'consultations' => 0,
            'prescriptions' => 0,
        ];
        $notifications = [];
        if ($patient) {
            $upcomingAppointments = \App\Models\Appointment::with(['doctor.user'])
                ->where('patient_id', $patient->id)
                ->where('appointment_date', '>=', now()->toDateString())
                ->orderBy('appointment_date')
                ->get();
            $recentPrescriptions = \App\Models\Prescription::whereHas('consultation.appointment', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->orderByDesc('id')->take(5)->get();
            $stats['appointments'] = \App\Models\Appointment::where('patient_id', $patient->id)->count();
            $stats['consultations'] = \App\Models\Consultation::whereHas('appointment', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->count();
            $stats['prescriptions'] = \App\Models\Prescription::whereHas('consultation.appointment', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->count();
            // Notification prochain RDV
            $nextRdv = $upcomingAppointments->first();
            if ($nextRdv) {
                $notifications[] = 'Prochain rendez-vous le ' . \Carbon\Carbon::parse($nextRdv->appointment_date)->format('d/m/Y') . ' à ' . $nextRdv->heure . ' avec Dr ' . $nextRdv->doctor->user->firstname . ' ' . $nextRdv->doctor->user->lastname;
            }
        }
        return view('patient.dashboard', compact('upcomingAppointments', 'recentPrescriptions', 'stats', 'notifications'));
    }
}
