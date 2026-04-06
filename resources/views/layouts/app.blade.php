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
<link rel="icon" href="https://steamesports.steamyourdreams.org/wp-content/uploads/2022/09/cropped-SYD_esports_sheild-03-32x32.png" sizes="32x32">
<link rel="icon" href="https://steamesports.steamyourdreams.org/wp-content/uploads/2022/09/cropped-SYD_esports_sheild-03-192x192.png" sizes="192x192">
<link rel="apple-touch-icon" href="https://steamesports.steamyourdreams.org/wp-content/uploads/2022/09/cropped-SYD_esports_sheild-03-180x180.png">
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
        .swal2-confirm-custom{
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
                <div class="logo">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo">
                    <div class="logo-text">
                        <p>EVENT & TOURNEY<br>MANAGEMENT</p>
                    </div>
                </div>
    
                <nav class="nav-menu">
                    {{-- Dashboard always visible --}}
                    <a href="{{ route('dashboard.index') }}"
                       class="nav-link-custom {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    @guest
                    <a href="{{ route('scoreboard.index') }}"
                    class="nav-link-custom {{ request()->routeIs('scoreboard.index') ? 'active' : '' }}">
                     Scoreboard
                 </a>
                    @endguest
    
                    @auth
                        @php $user = auth()->user(); @endphp
    
                        {{-- Admin: full access --}}
                        @role('admin')
                            <a href="{{ route('events.index') }}"
                               class="nav-link-custom {{ request()->routeIs('events.index') ? 'active' : '' }}">
                                Events
                            </a>
    
                            <!-- Boards Dropdown -->
                            <div class="nav-item dropdown">
                                <button class="nav-link-custom dropdown-toggle">The Boards</button>
                                <div class="dropdown-menu">
                                    <a href="{{ route('leaderboard.index') }}">Leaderboards</a>
                                    <a href="{{ route('scoreboard.index') }}">Scoreboards</a>
                                    <a href="#">Tournament Bracket</a>
                                </div>
                            </div>
    
                            <a href="{{ route('scoring.index') }}"
                               class="nav-link-custom {{ request()->routeIs('scoring.index') ? 'active' : '' }}">
                                Scoring
                            </a>
    
                            <!-- Q-Action Dropdown -->
                            <div class="nav-item dropdown">
                                <button class="nav-link-custom dropdown-toggle">Q-Action</button>
                                <div class="dropdown-menu">
                                    <a href="#" data-bs-target="#createEventModal" data-bs-toggle="modal">Create Event</a>
                                    <a href="#" data-bs-target="#addStudentModal" data-bs-toggle="modal">Add Player</a>
                                    <a href="#" data-bs-target="#addCardModal" data-bs-toggle="modal">Create Cards</a>
                                    <a href="#" data-bs-target="#assignCardModal" data-bs-toggle="modal">Assign Card</a>
                                </div>
                            </div>
    
                            <a href="{{ route('settings.index') }}"
                               class="nav-link-custom {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                                Settings
                            </a>
                        @else
                            {{-- Role-assigned users (non-admin) --}}
                            @if($user->roles->count() > 0)
                                <a href="{{ route('events.index') }}"
                                   class="nav-link-custom {{ request()->routeIs('events.index') ? 'active' : '' }}">
                                    Events
                                </a>
    
                                <!-- Boards Dropdown -->
                                <div class="nav-item dropdown">
                                    <button class="nav-link-custom dropdown-toggle">The Boards</button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('leaderboard.index') }}">Leaderboards</a>
                                        <a href="{{ route('scoreboard.index') }}">Scoreboards</a>
                                        <a href="#">Tournament Bracket</a>
                                    </div>
                                </div>
    
                                <a href="{{ route('scoring.index') }}"
                                   class="nav-link-custom {{ request()->routeIs('scoring.index') ? 'active' : '' }}">
                                    Scoring
                                </a>
                            @endif
                        @endrole
    
                        {{-- Logout for all logged-in users --}}
                        <a href="{{ route('logout') }}" class="nav-link-custom filled"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
    
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
    
                    @else
                        {{-- Login for guests --}}
                        <a href="{{ route('login') }}" class="nav-link-custom">Login</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')

        @stack('modals')
        @include('card.create')
        @include('card.assign-card-modal',[
            'cards' => \App\Models\Card::all()
        ])
        @include('card.assign-script')
        @include('events.modals.create-event', [
            'steamCategories' => \App\Models\SteamCategory::all()
        ])
        @include('students.modals.create-students',[
              'organizations' => \App\Models\Organization::all()
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
    @if(session('popup_error'))
    Swal.fire({
            icon: 'error',
            title: 'Warning!',
            text: '{{ session("popup_error") }}',
            confirmButtonText: 'OK',

            // Custom styling
            background: '#000000', // popup background black
            color: '#ffffff',      // text color white
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
    <script src="{{ asset('assets/scripts/main.js') }}"></script>
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
    @stack('scripts')

    
</body>

</html>
