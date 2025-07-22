<html>
<head>
    <meta charset="utf-8">
    <title>Reçu de paiement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 0 30px; }
        .footer { margin-top: 30px; font-size: 12px; color: #888; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reçu de paiement</h2>
    </div>
    <div class="content">
        <p><strong>Facture n° :</strong> {{ $bill->id }}</p>
        <p><strong>Montant :</strong> {{ $bill->total_amount ?? $bill->montant }} XOF</p>
        <p><strong>Date :</strong> {{ $bill->updated_at->format('d/m/Y H:i') }}</p>
        <p><strong>Statut :</strong> Payée</p>
    </div>
    <div class="footer">
        Merci pour votre paiement !<br>
        MediConnectHub
    </div>
</body>
</html> 