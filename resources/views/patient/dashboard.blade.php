@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2>Tableau de bord Patient</h2>
    <p>Bienvenue sur votre espace personnel. Ici, vous pouvez consulter vos rendez-vous, ordonnances et dossier médical.</p>
    <div class="my-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <a href="{{ route('medical-records.index') }}" class="btn btn-info">Voir mon dossier médical</a>
        <a href="{{ route('medical-records.create') }}" class="btn btn-success ms-2">Enregistrer un nouveau dossier médical</a>
        <a href="{{ route('appointments.request') }}" class="btn btn-primary ms-2">Prendre rendez-vous</a>
    </div>
    <div class="mb-4">
        <a href="{{ url('/medical-record-demo') }}" class="btn btn-primary">
            Enregistrer un dossier médical sécurisé
        </a>
    </div>
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
    @if(!empty($notifications))
        <div class="alert alert-warning mt-4">
            <ul class="mb-0">
                @foreach($notifications as $notif)
                    <li>{{ $notif }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="mt-5">
        <h4>Mes rendez-vous à venir</h4>
        @if(isset($upcomingAppointments) && $upcomingAppointments->count())
            <ul class="list-group">
                @foreach($upcomingAppointments as $rdv)
                    <li class="list-group-item">
                        Avec Dr {{ $rdv->doctor->user->firstname }} {{ $rdv->doctor->user->lastname }} le {{ \Carbon\Carbon::parse($rdv->appointment_date)->format('d/m/Y H:i') }}
                        @if($rdv->status === 'confirmed')
                            <a href="{{ route('teleconsultation.appointment', ['appointment' => $rdv->id]) }}" class="btn btn-success btn-sm ms-2">
                                <i class="bi bi-camera-video"></i> Rejoindre la téléconsultation
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">Aucun rendez-vous à venir.</div>
        @endif
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
    <div class="mt-5">
        <h4>Mes ordonnances récentes</h4>
        @if(isset($recentPrescriptions) && $recentPrescriptions->count())
            <ul class="list-group">
                @foreach($recentPrescriptions as $ord)
                    <li class="list-group-item">
                        <strong>Consultation du {{ $ord->consultation->date_consultation ?? 'N/A' }}</strong><br>
                        <span>{{ $ord->description }}</span><br>
                        <span class="text-muted">Médicaments : {{ $ord->medicaments }}</span>
                        <a href="{{ route('prescriptions.show', $ord) }}" class="btn btn-sm btn-outline-primary ms-2">Voir</a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">Aucune ordonnance récente.</div>
        @endif
    </div>
    <div class="mt-5 text-end">
        <a href="#" class="btn btn-secondary">Voir l'historique complet</a>
    </div>
</div>
@endsection
