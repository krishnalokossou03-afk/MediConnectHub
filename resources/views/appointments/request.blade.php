@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg p-4 w-100" style="max-width: 500px;">
        <h2 class="text-center mb-4">Prendre rendez-vous</h2>
        <form method="GET" action="{{ route('appointments.request') }}">
            <div class="form-group mb-3">
                <label for="specialty" class="fw-bold">Choisissez une spécialité médicale :</label>
                <select name="specialty" id="specialty" class="form-select" required>
                    <option value="">-- Sélectionner --</option>
                    @foreach($specialties as $spec)
                        <option value="{{ $spec }}" {{ request('specialty') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Voir les médecins disponibles</button>
        </form>
        @if(isset($doctors))
            <hr>
            <h4 class="text-center">Médecins disponibles</h4>
            @if($doctors->count())
                <ul class="list-group mb-3">
                    @foreach($doctors as $doctor)
                        <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div>
                                <span class="fw-bold">Dr {{ $doctor->user->firstname }} {{ $doctor->user->lastname }}</span>
                                <span class="text-muted">({{ $doctor->specialty }})</span>
                            </div>
                            <form method="POST" action="{{ route('appointments.book') }}" class="d-flex flex-wrap align-items-center gap-2 mt-2 mt-md-0">
                                @csrf
                                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                <input type="date" name="date" class="form-control form-control-sm" required>
                                <input type="time" name="heure" class="form-control form-control-sm" required>
                                <button type="submit" class="btn btn-success btn-sm">Prendre RDV</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-warning text-center">Aucun médecin disponible dans cette spécialité pour le moment.</div>
            @endif
        @endif
    </div>
</div>
@endsection
