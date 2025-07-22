<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'MediConnectHub')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-hover:hover { transform: scale(1.03); box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
        .sticky-top { position: sticky; top: 0; z-index: 1030; }
    </style>
</head>
<body class="bg-light">

    <!-- Navigation sticky -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('home') }}">MediConnectHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="{{ route('admin.quicklogin') }}"><i class="bi bi-shield-lock"></i> Admin</a></li>
                        <li class="nav-item"><a class="btn btn-primary ms-2" href="{{ route('select.role') }}">S'inscrire</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="#">{{ Auth::user()->name ?? Auth::user()->firstname }}</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-danger btn-sm ms-2">Déconnexion</button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main content -->
    <main class="py-4">
        @yield('content')
    </main>

    @stack('scripts')

    <!-- Scroll to top -->
    <button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="btn btn-primary position-fixed" style="bottom:30px;right:30px;z-index:999;display:none;" id="scrollTopBtn">
        <i class="bi bi-arrow-up"></i>
    </button>
    <script>
        window.onscroll = function() {
            document.getElementById('scrollTopBtn').style.display = (window.scrollY > 200) ? 'block' : 'none';
        };
    </script>

    <!-- Footer -->
    <footer id="mainFooter" class="bg-primary text-white text-center py-3" style="position:fixed;bottom:0;left:0;width:100%;z-index:1000;display:none;">
        &copy; {{ date('Y') }} MediConnectHub. Tous droits réservés.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Affiche le footer seulement quand on est en bas de page
        function toggleFooterOnScroll() {
            const footer = document.getElementById('mainFooter');
            const scrollPosition = window.innerHeight + window.scrollY;
            const pageHeight = document.body.offsetHeight;
            if (scrollPosition >= pageHeight - 2) {
                footer.style.display = 'block';
            } else {
                footer.style.display = 'none';
            }
        }
        window.addEventListener('scroll', toggleFooterOnScroll);
        window.addEventListener('resize', toggleFooterOnScroll);
        document.addEventListener('DOMContentLoaded', toggleFooterOnScroll);
    </script>
</body>
</html>