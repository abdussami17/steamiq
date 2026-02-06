<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title')</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/styles/style.css') }}">

<style>
    /* Improve font and icon visibility */
    .toast {
        font-family: Arial, sans-serif;
        font-size: 14px;
    }
    .toast-success {
        background-color: #51A351 !important;
    }
    .toast-error {
        background-color: #BD362F !important;
    }
</style>

</head>
<body>
    <div class="bg-pattern"></div>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">XR</div>
                    <div class="logo-text">
                        <h1>STEAM IQ</h1>
                        <p>EVENT MANAGEMENT</p>
                    </div>
                </div>
            
                <nav class="d-flex align-items-center gap-4">

                    {{-- Common nav links --}}
                    <nav class="d-flex align-items-center gap-4">

                        <a href="{{ route('dashboard.index') }}"
                           class="nav-link-custom {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                            Dashboard
                        </a>
                    
                        <a href="{{ route('events.index') }}"
                           class="nav-link-custom {{ request()->routeIs('events.*') ? 'active' : '' }}">
                            Events
                        </a>
                    
                        <a href="{{ route('player.index') }}"
                           class="nav-link-custom {{ request()->routeIs('player.*') ? 'active' : '' }}">
                            Players
                        </a>
                    
                        <a href="{{ route('tournaments.index') }}"
                           class="nav-link-custom {{ request()->routeIs('tournaments.*') ? 'active' : '' }}">
                            Tournaments
                        </a>
                    
                
                    @auth
                        {{-- username --}}
                        <span class="nav-link-custom disabled-link">
                            {{ Auth::user()->username }}
                        </span>
                
                        {{-- Logout link --}}
                        <a href="{{ route('logout') }}" class="nav-link-custom" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                
                        {{-- Hidden logout form --}}
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('register-login') }}" class="nav-link-custom">Login</a>
                    @endauth
                
                </nav>
                
                
            </div>
            
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toastr default options
        toastr.options = {
            "closeButton": true,        
            "progressBar": true,       
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        @if(session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if(session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error(@json($error));
            @endforeach
        @endif
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    
    <!-- Main JavaScript -->
    <script src="{{asset('assets/scripts/main.js')}}"></script>
</body>
</html>
