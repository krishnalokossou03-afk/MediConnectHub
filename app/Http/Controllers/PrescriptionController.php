<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class PrescriptionController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::with('consultation')->get();
        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        $consultations = Consultation::all();
        return view('prescriptions.create', compact('consultations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'medicaments' => 'required|string',
            'instructions' => 'nullable|string',
        ]);

        $prescription = Prescription::create($request->all());

        // Envoi de l'ordonnance PDF par email au patient
        $consultation = $prescription->consultation()->with('patient.user')->first();
        $user = $consultation && $consultation->patient ? $consultation->patient->user : null;
        if ($user && $user->email) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('prescriptions.pdf', ['prescription' => $prescription]);
            Mail::send([], [], function ($message) use ($user, $pdf, $prescription) {
                $message->to($user->email)
                    ->subject('Votre ordonnance MediConnectHub')
                    ->setBody('Bonjour,\n\nVeuillez trouver en pièce jointe votre ordonnance.\n\nMerci pour votre confiance.\nMediConnectHub', 'text/plain')
                    ->attachData($pdf->output(), 'ordonnance-'.$prescription->id.'.pdf');
            });
        }

        return redirect()->route('prescriptions.index')->with('success', 'Ordonnance enregistrée et envoyée au patient.');
    }

    public function show(Prescription $prescription)
    {
        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $consultations = Consultation::all();
        return view('prescriptions.edit', compact('prescription', 'consultations'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'medicaments' => 'required|string',
        ]);

        $prescription->update($request->all());

        return redirect()->route('prescriptions.index')->with('success', 'Ordonnance mise à jour.');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')->with('success', 'Ordonnance supprimée.');
    }

    public function downloadPdf($id)
    {
        $prescription = \App\Models\Prescription::with('consultation.doctor.user', 'consultation.patient.user')->findOrFail($id);
        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription'));
        return $pdf->download('ordonnance-'.$prescription->id.'.pdf');
    }
}
