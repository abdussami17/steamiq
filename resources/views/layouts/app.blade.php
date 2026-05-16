<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <style>
        /* ===== CRITICAL LOADER CSS (must be first) ===== */

        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;

            background: #fff;

            display: flex;
            align-items: center;
            justify-content: center;

            z-index: 999999;

            opacity: 1;
            transition: opacity .4s ease;
        }

        /* simple clean spinner */
        .loader-spinner {
            width: 48px;
            height: 48px;

            border: 4px solid #000;
            border-top-color: #fff;

            border-radius: 50%;

            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* fade out only */
        #page-loader.hide {
            opacity: 0;
            pointer-events: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles/style.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/responsive.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/mobile-hint.css') }}">
    <link rel="icon"
        href="{{ asset('assets/logo.png') }}"
        sizes="32x32">
    <link rel="icon"
        href="{{ asset('assets/logo.png') }}"
        sizes="192x192">
    <link rel="apple-touch-icon"
        href="{{ asset('assets/logo.png') }}">
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

        .swal2-confirm-custom {
            border: 2px solid #ccc;
        }
    </style>
    @stack('styles')

</head>

<body>
    <div id="page-loader">
        <div class="loader-spinner"></div>
    </div>
    <div class="bg-pattern"></div>
     <header>
         <div class="container">
             <div class="header-content">
     
                 {{-- Logo --}}
                 <a href="{{ route('dashboard.index') }}" class="logo">
                     <img src="{{ asset('assets/logo.png') }}" alt="Logo">
                     <div class="logo-text">
                         <p>EVENT &amp; TOURNEY<br>MANAGEMENT</p>
                     </div>
                 </a>
     
                 {{-- Hamburger (mobile only) --}}
                 <button class="hamburger" id="hamburger"
                         aria-label="Open menu"
                         aria-expanded="false"
                         aria-controls="nav-menu">
                     <span></span>
                     <span></span>
                     <span></span>
                 </button>
     
                 {{-- Navigation --}}
                 <nav class="nav-menu" id="nav-menu" role="navigation">
     
                     {{-- Dashboard — always visible --}}
                     <a href="{{ route('dashboard.index') }}"
                        class="nav-link-custom {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                         Dashboard
                     </a>
     
                     @guest
                         <a href="{{ route('scoreboard.index') }}"
                            class="nav-link-custom {{ request()->routeIs('scoreboard.index') ? 'active' : '' }}">
                             Scoreboard
                         </a>
                         <a href="{{ route('bracket.index') }}"
                            class="nav-link-custom {{ request()->routeIs('bracket.index') ? 'active' : '' }}">
                             Tournament Bracket
                         </a>
                     @endguest
     
                     @auth
                         @php $user = auth()->user(); @endphp
     
                         @role('admin')
                             {{-- Admin: full access --}}
                             <a href="{{ route('events.index') }}"
                                class="nav-link-custom {{ request()->routeIs('events.index') ? 'active' : '' }}">
                                 Events
                             </a>
     
                             <div class="nav-item" id="dd-boards">
                                 <button class="nav-link-custom dropdown-toggle"
                                         aria-haspopup="true"
                                         aria-expanded="false"
                                         onclick="toggleDropdown('dd-boards')">
                                     The Boards
                                     <span class="nav-chevron"><i data-lucide="chevron-down"></i></span>
                                 </button>
                                 <div class="dropdown-menu" role="menu">
                                     <a href="{{ route('leaderboard.index') }}" role="menuitem">Leaderboards</a>
                                     <a href="{{ route('scoreboard.index') }}" role="menuitem">Scoreboards</a>
                                     <a href="{{ route('bracket.index') }}" role="menuitem">Tournament Bracket</a>
                                 </div>
                             </div>
                       @can('show_roster')
                       <a href="{{ route('rosters.index') }}"
                       class="nav-link-custom {{ request()->routeIs('rosters.index') ? 'active' : '' }}">
                        Roster
                    </a>
                    @endcan
                             <a href="{{ route('scoring.index') }}"
                                class="nav-link-custom {{ request()->routeIs('scoring.index') ? 'active' : '' }}">
                                 Scoring
                             </a>
     
                         @else
                             {{-- Role-assigned non-admin users --}}
                             @if ($user->roles->count() > 0)
                                 <a href="{{ route('events.index') }}"
                                    class="nav-link-custom {{ request()->routeIs('events.index') ? 'active' : '' }}">
                                     Events
                                 </a>
                               
                                 <div class="nav-item" id="dd-boards">
                                     <button class="nav-link-custom dropdown-toggle"
                                             aria-haspopup="true"
                                             aria-expanded="false"
                                             onclick="toggleDropdown('dd-boards')">
                                         The Boards
                                         <span class="nav-chevron"><i data-lucide="chevron-down"></i></span>
                                     </button>
                                     <div class="dropdown-menu" role="menu">
                                         <a href="{{ route('leaderboard.index') }}" role="menuitem">Leaderboards</a>
                                         <a href="{{ route('scoreboard.index') }}" role="menuitem">Scoreboards</a>
                                         <a href="{{ route('bracket.index') }}" role="menuitem">Tournament Bracket</a>
                                     </div>
                                 </div>
     
                                 <a href="{{ route('scoring.index') }}"
                                    class="nav-link-custom {{ request()->routeIs('scoring.index') ? 'active' : '' }}">
                                     Scoring
                                 </a>
                             @endif
                         @endrole
     
                         {{-- Q-Action — all authenticated users --}}
                         <div class="nav-item" id="dd-qa">
                             <button class="nav-link-custom dropdown-toggle"
                                     aria-haspopup="true"
                                     aria-expanded="false"
                                     onclick="toggleDropdown('dd-qa')">
                                 Q-Action
                                 <span class="nav-chevron"><i data-lucide="chevron-down"></i></span>
                             </button>
                             <div class="dropdown-menu" role="menu">

                                 @can('create_event')
                                 <a href="{{ route('events.index', ['tab' => 'organizations-tab']) }}"
                                    class="nav-link-custom">
                                     Organizations
                                 </a>
                                 
                                 <a href="{{ route('events.index', ['tab' => 'teams-tab']) }}"
                                    class="nav-link-custom">
                                     Teams
                                 </a>
                                 
                                 <a href="{{ route('events.index', ['tab' => 'players-tab']) }}"
                                    class="nav-link-custom">
                                     Players
                                 </a>
                                     <a href="#" role="menuitem"
                                        data-bs-target="#createEventModal"
                                        data-bs-toggle="modal">
                                         Create Event
                                     </a>
                                 @endcan
                                 @can('create_player')
                                     <a href="#" role="menuitem"
                                        data-bs-target="#addStudentModal"
                                        data-bs-toggle="modal">
                                         Add Player
                                     </a>
                                 @endcan
                                 @role('admin')
                                     <div class="dropdown-divider"></div>
                                     <a href="#" role="menuitem"
                                        data-bs-target="#addCardModal"
                                        data-bs-toggle="modal">
                                         Create Cards
                                     </a>
                                 @endrole
                                 <a href="#" role="menuitem"
                                    data-bs-target="#assignCardModal"
                                    data-bs-toggle="modal">
                                     Assign Card
                                 </a>
                             </div>
                         </div>
     
                         <a href="{{ route('settings.index') }}"
                            class="nav-link-custom {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                             Settings
                         </a>
     
                         {{-- Logout --}}
                         <a href="{{ route('logout') }}" class="nav-link-custom filled"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             Logout
                         </a>
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                             @csrf
                         </form>
     
                     @else
                         {{-- Guest login --}}
                         <a href="{{ route('login') }}" class="nav-link-custom filled">Login</a>
                     @endauth
     
                 </nav>
             </div>
         </div>
     </header>

    <main>
        @yield('content')

        @stack('modals')
        @include('card.create')
        @include('card.assign-card-modal', [
            'cards' => \App\Models\Card::all(),
        ])
        @include('card.assign-script')
        @include('events.modals.create-event', [
            'steamCategories' => \App\Models\SteamCategory::all(),
        ])
        @include('students.modals.create-students', [
            'organizations' => \App\Models\Organization::all(),
        ])

    </main>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
        // SweetAlert popup for backend errors
        @if (session('popup_error'))
            Swal.fire({
                icon: 'error',
                title: 'Warning!',
                text: '{{ session('popup_error') }}',
                confirmButtonText: 'OK',

                // Custom styling
                background: '#000000', // popup background black
                color: '#ffffff', // text color white
                confirmButtonColor: '#000000', // button background black
                customClass: {
                    confirmButton: 'swal2-confirm-custom' // custom class for border
                }
            });
        @endif

        @if (session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if (session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
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
  <script src="{{ asset('assets/scripts/main.js?v=' . time()) }}"></script>
  @include('components.mobile-hint')
  <script src="{{ asset('assets/scripts/mobile-hint.js?v=' . time()) }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all date inputs
            flatpickr('input[type="date"]', {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // Initialize all time inputs
            flatpickr('input[type="time"]', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                allowInput: true
            });

            // Initialize all datetime-local inputs
            flatpickr('input[type="datetime-local"]', {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                allowInput: true
            });
        });
    </script>

    <script>
        window.addEventListener("load", function() {
            const loader = document.getElementById("page-loader");

            loader.classList.add("hide");

            setTimeout(() => {
                loader.remove();
            }, 400);
        });
    </script>
    <script>
        /* ================================================================
   HEADER JS — Dropdowns + Hamburger + Lucide icons
   Place this before </body>, after Lucide CDN script tag
   ================================================================ */

document.addEventListener('DOMContentLoaded', function () {

/* ── Lucide icons ───────────────────────────── */
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

/* ── Dropdown toggle ────────────────────────── */
window.toggleDropdown = function (id) {
    const item = document.getElementById(id);
    if (!item) return;
    const isOpen = item.classList.contains('open');
    closeAllDropdowns();
    if (!isOpen) {
        item.classList.add('open');
        const btn = item.querySelector('.dropdown-toggle');
        if (btn) btn.setAttribute('aria-expanded', 'true');
    }
};

function closeAllDropdowns() {
    document.querySelectorAll('.nav-item.open').forEach(function (el) {
        el.classList.remove('open');
        const btn = el.querySelector('.dropdown-toggle');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    });
}

/* ── Close on outside click ─────────────────── */
document.addEventListener('click', function (e) {
    if (!e.target.closest('.nav-item')) {
        closeAllDropdowns();
    }
});

/* ── Hamburger ──────────────────────────────── */
var hamburger = document.getElementById('hamburger');
var navMenu   = document.getElementById('nav-menu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', function () {
        var open = navMenu.classList.toggle('open');
        hamburger.classList.toggle('open', open);
        hamburger.setAttribute('aria-expanded', String(open));
        if (!open) closeAllDropdowns();
    });
}

/* ── Close mobile menu on plain link click ───── */
document.querySelectorAll('#nav-menu .nav-link-custom:not(.dropdown-toggle)').forEach(function (link) {
    link.addEventListener('click', function () {
        if (navMenu) navMenu.classList.remove('open');
        if (hamburger) {
            hamburger.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
        }
        closeAllDropdowns();
    });
});

/* ── Escape key closes everything ───────────── */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeAllDropdowns();
        if (navMenu)   navMenu.classList.remove('open');
        if (hamburger) {
            hamburger.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
        }
    }
});

});
    </script>
    @stack('scripts')


</body>

</html>
