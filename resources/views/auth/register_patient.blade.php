@extends('layouts.app')
@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height:70vh;">
  <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
    <h2 class="mb-4 text-center">Inscription Patient</h2>
    <form method="POST" action="{{ route('register', ['role' => 'patient']) }}">
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
        <label class="form-label">Date de naissance</label>
        <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
        @error('birth_date')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Genre</label>
        <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
          <option value="">Sélectionnez votre genre</option>
          <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Homme</option>
          <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femme</option>
          <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Autre</option>
        </select>
        @error('gender')
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
      <button type="submit" class="btn btn-primary w-100">S'inscrire comme Patient</button>
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