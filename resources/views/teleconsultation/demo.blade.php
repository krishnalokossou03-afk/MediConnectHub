@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2 class="mb-4">Démo de visioconférence patient & médecin</h2>
    <div id="jitsi-container" style="height: 600px; width: 100%; max-width: 900px; margin: 0 auto;"></div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-4">Quitter la démo</a>
    <div class="card mt-4 mx-auto" style="max-width: 900px;">
        <div class="card-header">Chat en direct (démo)</div>
        <div class="card-body" style="height: 200px; overflow-y: auto;" id="chat-messages-demo">
            <div><strong>Dr. Jean Martin :</strong> Bonjour Krishna, comment puis-je vous aider aujourd'hui ?</div>
            <div class="text-end"><strong>Krishna :</strong> Bonjour docteur, j'ai des douleurs à la poitrine depuis ce matin.</div>
        </div>
        <div class="card-footer">
            <form id="chat-form-demo" class="d-flex">
                <input type="text" id="chat-input-demo" class="form-control me-2" placeholder="Écrire un message..." autocomplete="off">
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://meet.jit.si/external_api.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Génère un nom de salle unique pour la démo (aléatoire à chaque chargement)
        const randomRoom = 'demo_' + Math.random().toString(36).substring(2, 10);
        const domain = "meet.jit.si";
        const options = {
            roomName: "mediconnecthub-" + randomRoom,
            width: "100%",
            height: 600,
            parentNode: document.getElementById('jitsi-container'),
            userInfo: {
                displayName: "Démo Utilisateur"
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);

        // Chat démo (pas connecté au backend)
        const chatForm = document.getElementById('chat-form-demo');
        const chatInput = document.getElementById('chat-input-demo');
        const chatMessages = document.getElementById('chat-messages-demo');
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (!text) return;
            chatMessages.innerHTML += `<div class='text-end'><strong>Vous :</strong> ${text}</div>`;
            chatInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    });
</script>
@endpush
