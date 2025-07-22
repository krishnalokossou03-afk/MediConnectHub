@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Retour de paiement FedaPay</h2>
    @if($bill)
        <div class="alert alert-success">Votre paiement a été validé !</div>
        <div class="card mb-4">
            <div class="card-header">Reçu de paiement</div>
            <div class="card-body">
                <p><strong>Facture n° :</strong> {{ $bill->id }}</p>
                <p><strong>Montant :</strong> {{ $bill->total_amount ?? $bill->montant }} XOF</p>
                <p><strong>Date :</strong> {{ $bill->updated_at->format('d/m/Y H:i') }}</p>
                <p><strong>Statut :</strong> <span class="badge bg-success">Payée</span></p>
                <a href="{{ route('bills.receipt.pdf', $bill) }}" class="btn btn-outline-primary">Télécharger le reçu (PDF)</a>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            Merci ! Le paiement a été traité. Veuillez vérifier votre compte FedaPay pour le statut final.
        </div>
    @endif
    <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
</div>
@endsection 