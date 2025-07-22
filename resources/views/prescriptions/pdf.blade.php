<html>
<head>
    <meta charset="utf-8">
    <title>Ordonnance</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 0 30px; }
        .footer { margin-top: 30px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Ordonnance</h2>
    </div>
    <div class="content">
        <p><strong>Ordonnance n° :</strong> {{ $prescription->id }}</p>
        <p><strong>Médecin :</strong> {{ $prescription->consultation->doctor->user->firstname ?? '' }} {{ $prescription->consultation->doctor->user->lastname ?? '' }}</p>
        <p><strong>Patient :</strong> {{ $prescription->consultation->patient->user->firstname ?? '' }} {{ $prescription->consultation->patient->user->lastname ?? '' }}</p>
        <p><strong>Date :</strong> {{ $prescription->created_at->format('d/m/Y') }}</p>
        <p><strong>Médicaments prescrits :</strong><br>{{ $prescription->medicaments }}</p>
        @if($prescription->instructions)
            <p><strong>Instructions :</strong><br>{{ $prescription->instructions }}</p>
        @endif
    </div>
    <div class="footer">
        MediConnectHub
    </div>
</body>
</html> 