@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Connexion Administrateur</div>
                <div class="card-body text-center">
                    <a href="{{ route('admin.quicklogin') }}" class="btn btn-primary btn-lg w-100">Connexion rapide Admin</a>
                </div>
            </div>
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
