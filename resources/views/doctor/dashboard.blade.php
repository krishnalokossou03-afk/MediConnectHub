@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2>Tableau de bord Médecin</h2>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Rendez-vous</h5>
                    <p class="display-6">{{ $stats['appointments'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Consultations</h5>
                    <p class="display-6">{{ $stats['consultations'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Ordonnances</h5>
                    <p class="display-6">{{ $stats['prescriptions'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h4>Nouveaux rendez-vous à confirmer</h4>
        @if($newAppointments->count())
            <ul class="list-group mb-3">
                @foreach($newAppointments as $rdv)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $rdv->patient->user->firstname }} {{ $rdv->patient->user->lastname }} le {{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }} à {{ $rdv->heure }}
                        <div>
                            <form method="POST" action="{{ route('doctor.appointments.confirm', $rdv) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Confirmer</button>
                            </form>
                            <form method="POST" action="{{ route('doctor.appointments.refuse', $rdv) }}" class="d-inline ms-1">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">Aucun nouveau rendez-vous à confirmer.</div>
        @endif
    </div>
    <div class="mt-5">
        <h4>Rendez-vous à venir</h4>
        @if($upcomingAppointments->count())
            <ul class="list-group mb-3">
                @foreach($upcomingAppointments as $rdv)
                    <li class="list-group-item">
                        {{ $rdv->patient->user->firstname }} {{ $rdv->patient->user->lastname }} le {{ \Carbon\Carbon::parse($rdv->appointment_date)->format('d/m/Y H:i') }}
                        @if($rdv->status === 'confirmed')
                            <a href="{{ route('teleconsultation.appointment', ['appointment' => $rdv->id]) }}" class="btn btn-success btn-sm ms-2">
                                <i class="bi bi-camera-video"></i> Démarrer la téléconsultation
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">Aucun rendez-vous à venir.</div>
        @endif
    </div>
    <div class="mt-5">
        <h4>Consultations récentes</h4>
        @if($recentConsultations->count())
            <ul class="list-group mb-3">
                @foreach($recentConsultations as $consult)
                    <li class="list-group-item">
                        {{ $consult->patient->user->firstname }} {{ $consult->patient->user->lastname }} le {{ \Carbon\Carbon::parse($consult->date_consultation)->format('d/m/Y') }}<br>
                        <span class="text-muted">Diagnostic : {{ $consult->diagnostic }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">Aucune consultation récente.</div>
        @endif
    </div>
    <div class="mt-5">
        <h4>Mes patients</h4>
        @if($patients->count())
            <ul class="list-group mb-3">
                @foreach($patients as $pat)
                    <li class="list-group-item">
                        {{ $pat->user->firstname }} {{ $pat->user->lastname }}
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">Aucun patient suivi.</div>
        @endif
    </div>
    <div class="mt-5">
        <h4>Ordonnances à valider</h4>
        @if($pendingPrescriptions->count())
            <ul class="list-group mb-3">
                @foreach($pendingPrescriptions as $ord)
                    <li class="list-group-item">
                        <strong>Consultation du {{ $ord->consultation->date_consultation ?? 'N/A' }}</strong><br>
                        <span>{{ $ord->description }}</span><br>
                        <span class="text-muted">Médicaments : {{ $ord->medicaments }}</span>
                        <form method="POST" action="{{ route('doctor.prescriptions.validate', $ord) }}" class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Valider</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">Aucune ordonnance à valider.</div>
        @endif
    </div>
    <div class="mt-5 text-end">
        <a href="{{ route('doctor.history') }}" class="btn btn-secondary">Voir l'historique complet</a>
    </div>
    @if(isset($upcomingAppointments) && $upcomingAppointments->count())
        @php
            $firstRdv = $upcomingAppointments->first();
            $room = 'rdv-' . ($firstRdv->id ?? '');
        @endphp
        <a href="{{ route('teleconsultation.room', ['room' => $room]) }}" class="btn btn-success mb-3">
            <i class="bi bi-camera-video"></i> Rejoindre la téléconsultation
        </a>
    @endif
</div>
@endsection
