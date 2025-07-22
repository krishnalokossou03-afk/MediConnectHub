@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Détail de l'ordonnance</h2>
    <div class="card mb-4">
        <div class="card-header">Ordonnance n° {{ $prescription->id }}</div>
        <div class="card-body">
            <p><strong>Médecin :</strong> {{ $prescription->consultation->doctor->user->firstname ?? '' }} {{ $prescription->consultation->doctor->user->lastname ?? '' }}</p>
            <p><strong>Patient :</strong> {{ $prescription->consultation->patient->user->firstname ?? '' }} {{ $prescription->consultation->patient->user->lastname ?? '' }}</p>
            <p><strong>Date :</strong> {{ $prescription->created_at->format('d/m/Y') }}</p>
            <p><strong>Médicaments prescrits :</strong><br>{{ $prescription->medicaments }}</p>
            @if($prescription->instructions)
                <p><strong>Instructions :</strong><br>{{ $prescription->instructions }}</p>
            @endif
            <a href="{{ route('prescriptions.pdf', $prescription) }}" class="btn btn-outline-primary">Télécharger l'ordonnance (PDF)</a>
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Retour</a>
</div>
@endsection 