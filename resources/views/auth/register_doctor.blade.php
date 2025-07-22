@extends('layouts.app')
@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height:70vh;">
  <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
    <h2 class="mb-4 text-center">Inscription Médecin</h2>
    <form method="POST" action="{{ route('register', ['role' => 'doctor']) }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Prénom</label>
        <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror" value="{{ old('firstname') }}" required>
        @error('firstname')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror" value="{{ old('lastname') }}" required>
        @error('lastname')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
        @error('phone')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Spécialités (séparées par virgule)</label>
        <input type="text" name="specialty" class="form-control @error('specialty') is-invalid @enderror" value="{{ old('specialty') }}" placeholder="Cardiologie, Neurologie" required>
        @error('specialty')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Biographie</label>
        <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3">{{ old('bio') }}</textarea>
        @error('bio')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <div class="input-group">
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" required>
          <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-success w-100">S'inscrire comme Médecin</button>
    </form>
    <div class="mt-3 text-center">
      <a href="{{ route('login') }}">Déjà inscrit ? Se connecter</a>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
function togglePassword(id) {
    var input = document.getElementById(id);
    var icon = document.getElementById('toggleIcon');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection