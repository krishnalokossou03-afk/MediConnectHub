<?php
namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with('patient')->get();
        return view('consultations.index', compact('consultations'));
    }

    public function create()
    {
        $patients = Patient::all();
        return view('consultations.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'summary' => 'nullable|string',
        ]);

        Consultation::create($validated);
        return redirect()->route('consultations.index')->with('success', 'Consultation enregistrée.');
    }

    public function show(Consultation $consultation)
    {
        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation)
    {
        $patients = Patient::all();
        return view('consultations.edit', compact('consultation', 'patients'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'summary' => 'nullable|string',
        ]);

        $consultation->update($validated);
        return redirect()->route('consultations.index')->with('success', 'Consultation mise à jour.');
    }

    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        return redirect()->route('consultations.index')->with('success', 'Consultation supprimée.');
    }
}

