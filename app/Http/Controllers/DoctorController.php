<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('user')->get();
        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        // On récupère uniquement les utilisateurs qui ne sont pas encore docteurs
        $users = User::doesntHave('doctor')->get();
        return view('doctors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:doctors,user_id',
            'specialty' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        Doctor::create($request->only('user_id', 'specialty', 'phone', 'bio'));

        return redirect()->route('doctors.index')->with('success', 'Docteur ajouté avec succès.');
    }

    public function show(Doctor $doctor)
    {
        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $users = User::all();
        return view('doctors.edit', compact('doctor', 'users'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'specialty' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $doctor->update($request->only('specialty', 'phone', 'bio'));

        return redirect()->route('doctors.index')->with('success', 'Docteur mis à jour.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Docteur supprimé.');
    }

    public function dashboardDoctor()
    {
        $user = Auth::user();
        $doctor = $user->doctor ?? null;
        $stats = [
            'appointments' => 0,
            'consultations' => 0,
            'prescriptions' => 0,
        ];
        $upcomingAppointments = collect();
        $newAppointments = collect();
        $recentConsultations = collect();
        $patients = collect();
        $pendingPrescriptions = collect();
        if ($doctor) {
            $stats['appointments'] = \App\Models\Appointment::where('doctor_id', $doctor->id)->count();
            $stats['consultations'] = \App\Models\Consultation::where('doctor_id', $doctor->id)->count();
            $stats['prescriptions'] = \App\Models\Prescription::whereHas('consultation', function($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })->count();
            $upcomingAppointments = \App\Models\Appointment::with(['patient.user'])
                ->where('doctor_id', $doctor->id)
                ->where('appointment_date', '>=', now()->toDateString())
                ->orderBy('appointment_date')
                ->get();
            $newAppointments = \App\Models\Appointment::with(['patient.user'])
                ->where('doctor_id', $doctor->id)
                ->where('status', 'pending')
                ->orderBy('appointment_date')
                ->get();
            $recentConsultations = \App\Models\Consultation::with(['patient.user'])
                ->where('doctor_id', $doctor->id)
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
            $patients = \App\Models\Patient::whereIn('id',
                \App\Models\Consultation::where('doctor_id', $doctor->id)
                    ->with('appointment')
                    ->get()
                    ->pluck('appointment.patient_id')
                    ->unique()
            )->with('user')->get();
            $pendingPrescriptions = \App\Models\Prescription::whereHas('consultation', function($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })->whereNull('validated_at')->get();
        }
        return view('doctor.dashboard', compact('stats', 'upcomingAppointments', 'newAppointments', 'recentConsultations', 'patients', 'pendingPrescriptions'));
    }

    public function confirmAppointment($id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        $appointment->status = 'confirmed';
        $appointment->save();
        return back()->with('success', 'Rendez-vous confirmé.');
    }
    public function refuseAppointment($id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        $appointment->status = 'refused';
        $appointment->save();
        return back()->with('success', 'Rendez-vous refusé.');
    }
    public function validatePrescription($id)
    {
        $prescription = \App\Models\Prescription::findOrFail($id);
        $prescription->validated_at = now();
        $prescription->save();
        return back()->with('success', 'Ordonnance validée.');
    }
    public function history()
    {
        $user = Auth::user();
        $doctor = $user->doctor ?? null;
        $appointments = collect();
        $consultations = collect();
        $prescriptions = collect();
        if ($doctor) {
            $appointments = \App\Models\Appointment::with(['patient.user'])
                ->where('doctor_id', $doctor->id)
                ->orderByDesc('date')
                ->get();
            $consultations = \App\Models\Consultation::with(['patient.user'])
                ->where('doctor_id', $doctor->id)
                ->orderByDesc('created_at')
                ->get();
            $prescriptions = \App\Models\Prescription::whereHas('consultation', function($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })->orderByDesc('id')->get();
        }
        return view('doctor.history', compact('appointments', 'consultations', 'prescriptions'));
    }
}
