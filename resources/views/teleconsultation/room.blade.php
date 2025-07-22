@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2 class="mb-4">Téléconsultation en ligne</h2>
    <div id="jitsi-container" style="height: 600px; width: 100%; max-width: 900px; margin: 0 auto;"></div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-4">Quitter la téléconsultation</a>
    <div class="card mt-4 mx-auto" style="max-width: 900px;">
        <div class="card-header">Chat en direct</div>
        <div class="card-body" style="height: 200px; overflow-y: auto;" id="chat-messages"></div>
        <div class="card-footer">
            <form id="chat-form" class="d-flex">
                <input type="text" id="chat-input" class="form-control me-2" placeholder="Écrire un message..." autocomplete="off">
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
        const domain = "meet.jit.si";
        const options = {
            roomName: "mediconnecthub-{{ $room }}",
            width: "100%",
            height: 600,
            parentNode: document.getElementById('jitsi-container'),
            userInfo: {
                displayName: "{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}"
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);

        // Chat AJAX
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');
        const appointmentId = "{{ $appointment->id }}";
        function fetchMessages() {
            fetch(`/teleconsultation/chat/${appointmentId}`)
                .then(res => res.json())
                .then(data => {
                    chatMessages.innerHTML = data.map(msg => `<div><strong>${msg.user} :</strong> ${msg.text}</div>`).join('');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (!text) return;
            fetch(`/teleconsultation/chat/${appointmentId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ text })
            }).then(() => {
                chatInput.value = '';
                fetchMessages();
            });
        });
        setInterval(fetchMessages, 2000);
        fetchMessages();
    });
</script>
@endpush
