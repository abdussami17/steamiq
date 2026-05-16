<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title'); ?></title>

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
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/style.css?v=' . time())); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/responsive.css?v=' . time())); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/mobile-hint.css')); ?>">
    <link rel="icon"
        href="<?php echo e(asset('assets/logo.png')); ?>"
        sizes="32x32">
    <link rel="icon"
        href="<?php echo e(asset('assets/logo.png')); ?>"
        sizes="192x192">
    <link rel="apple-touch-icon"
        href="<?php echo e(asset('assets/logo.png')); ?>">
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
    <?php echo $__env->yieldPushContent('styles'); ?>

</head>

<body>
    <div id="page-loader">
        <div class="loader-spinner"></div>
    </div>
    <div class="bg-pattern"></div>
     <header>
         <div class="container">
             <div class="header-content">
     
                 
                 <a href="<?php echo e(route('dashboard.index')); ?>" class="logo">
                     <img src="<?php echo e(asset('assets/logo.png')); ?>" alt="Logo">
                     <div class="logo-text">
                         <p>EVENT &amp; TOURNEY<br>MANAGEMENT</p>
                     </div>
                 </a>
     
                 
                 <button class="hamburger" id="hamburger"
                         aria-label="Open menu"
                         aria-expanded="false"
                         aria-controls="nav-menu">
                     <span></span>
                     <span></span>
                     <span></span>
                 </button>
     
                 
                 <nav class="nav-menu" id="nav-menu" role="navigation">
     
                     
                     <a href="<?php echo e(route('dashboard.index')); ?>"
                        class="nav-link-custom <?php echo e(request()->routeIs('dashboard.index') ? 'active' : ''); ?>">
                         Dashboard
                     </a>
     
                     <?php if(auth()->guard()->guest()): ?>
                         <a href="<?php echo e(route('scoreboard.index')); ?>"
                            class="nav-link-custom <?php echo e(request()->routeIs('scoreboard.index') ? 'active' : ''); ?>">
                             Scoreboard
                         </a>
                         <a href="<?php echo e(route('bracket.index')); ?>"
                            class="nav-link-custom <?php echo e(request()->routeIs('bracket.index') ? 'active' : ''); ?>">
                             Tournament Bracket
                         </a>
                     <?php endif; ?>
     
                     <?php if(auth()->guard()->check()): ?>
                         <?php $user = auth()->user(); ?>
     
                         <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
                             
                             <a href="<?php echo e(route('events.index')); ?>"
                                class="nav-link-custom <?php echo e(request()->routeIs('events.index') ? 'active' : ''); ?>">
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
                                     <a href="<?php echo e(route('leaderboard.index')); ?>" role="menuitem">Leaderboards</a>
                                     <a href="<?php echo e(route('scoreboard.index')); ?>" role="menuitem">Scoreboards</a>
                                     <a href="<?php echo e(route('bracket.index')); ?>" role="menuitem">Tournament Bracket</a>
                                 </div>
                             </div>
                             <a href="<?php echo e(route('rosters.index')); ?>"
                             class="nav-link-custom <?php echo e(request()->routeIs('rosters.index') ? 'active' : ''); ?>">
                              Roster
                          </a>
                             <a href="<?php echo e(route('scoring.index')); ?>"
                                class="nav-link-custom <?php echo e(request()->routeIs('scoring.index') ? 'active' : ''); ?>">
                                 Scoring
                             </a>
     
                         <?php else: ?>
                             
                             <?php if($user->roles->count() > 0): ?>
                                 <a href="<?php echo e(route('events.index')); ?>"
                                    class="nav-link-custom <?php echo e(request()->routeIs('events.index') ? 'active' : ''); ?>">
                                     Events
                                 </a>
                                 <a href="<?php echo e(route('rosters.index')); ?>"
                                 class="nav-link-custom <?php echo e(request()->routeIs('rosters.index') ? 'active' : ''); ?>">
                                  Roster
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
                                         <a href="<?php echo e(route('leaderboard.index')); ?>" role="menuitem">Leaderboards</a>
                                         <a href="<?php echo e(route('scoreboard.index')); ?>" role="menuitem">Scoreboards</a>
                                         <a href="<?php echo e(route('bracket.index')); ?>" role="menuitem">Tournament Bracket</a>
                                     </div>
                                 </div>
     
                                 <a href="<?php echo e(route('scoring.index')); ?>"
                                    class="nav-link-custom <?php echo e(request()->routeIs('scoring.index') ? 'active' : ''); ?>">
                                     Scoring
                                 </a>
                             <?php endif; ?>
                         <?php endif; ?>
     
                         
                         <div class="nav-item" id="dd-qa">
                             <button class="nav-link-custom dropdown-toggle"
                                     aria-haspopup="true"
                                     aria-expanded="false"
                                     onclick="toggleDropdown('dd-qa')">
                                 Q-Action
                                 <span class="nav-chevron"><i data-lucide="chevron-down"></i></span>
                             </button>
                             <div class="dropdown-menu" role="menu">

                                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_event')): ?>
                                 <a href="<?php echo e(route('events.index', ['tab' => 'organizations-tab'])); ?>"
                                    class="nav-link-custom">
                                     Organizations
                                 </a>
                                 
                                 <a href="<?php echo e(route('events.index', ['tab' => 'teams-tab'])); ?>"
                                    class="nav-link-custom">
                                     Teams
                                 </a>
                                 
                                 <a href="<?php echo e(route('events.index', ['tab' => 'players-tab'])); ?>"
                                    class="nav-link-custom">
                                     Players
                                 </a>
                                     <a href="#" role="menuitem"
                                        data-bs-target="#createEventModal"
                                        data-bs-toggle="modal">
                                         Create Event
                                     </a>
                                 <?php endif; ?>
                                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_player')): ?>
                                     <a href="#" role="menuitem"
                                        data-bs-target="#addStudentModal"
                                        data-bs-toggle="modal">
                                         Add Player
                                     </a>
                                 <?php endif; ?>
                                 <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
                                     <div class="dropdown-divider"></div>
                                     <a href="#" role="menuitem"
                                        data-bs-target="#addCardModal"
                                        data-bs-toggle="modal">
                                         Create Cards
                                     </a>
                                 <?php endif; ?>
                                 <a href="#" role="menuitem"
                                    data-bs-target="#assignCardModal"
                                    data-bs-toggle="modal">
                                     Assign Card
                                 </a>
                             </div>
                         </div>
     
                         <a href="<?php echo e(route('settings.index')); ?>"
                            class="nav-link-custom <?php echo e(request()->routeIs('settings.index') ? 'active' : ''); ?>">
                             Settings
                         </a>
     
                         
                         <a href="<?php echo e(route('logout')); ?>" class="nav-link-custom filled"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             Logout
                         </a>
                         <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                             <?php echo csrf_field(); ?>
                         </form>
     
                     <?php else: ?>
                         
                         <a href="<?php echo e(route('login')); ?>" class="nav-link-custom filled">Login</a>
                     <?php endif; ?>
     
                 </nav>
             </div>
         </div>
     </header>

    <main>
        <?php echo $__env->yieldContent('content'); ?>

        <?php echo $__env->yieldPushContent('modals'); ?>
        <?php echo $__env->make('card.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('card.assign-card-modal', [
            'cards' => \App\Models\Card::all(),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('card.assign-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('events.modals.create-event', [
            'steamCategories' => \App\Models\SteamCategory::all(),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('students.modals.create-students', [
            'organizations' => \App\Models\Organization::all(),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
        <?php if(session('popup_error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Warning!',
                text: '<?php echo e(session('popup_error')); ?>',
                confirmButtonText: 'OK',

                // Custom styling
                background: '#000000', // popup background black
                color: '#ffffff', // text color white
                confirmButtonColor: '#000000', // button background black
                customClass: {
                    confirmButton: 'swal2-confirm-custom' // custom class for border
                }
            });
        <?php endif; ?>

        <?php if(session('success')): ?>
            toastr.success(<?php echo json_encode(session('success'), 15, 512) ?>);
        <?php endif; ?>

        <?php if(session('error')): ?>
            toastr.error(<?php echo json_encode(session('error'), 15, 512) ?>);
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                toastr.error(<?php echo json_encode($error, 15, 512) ?>);
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>


    <!-- Main JavaScript -->
  <script src="<?php echo e(asset('assets/scripts/main.js?v=' . time())); ?>"></script>
  <?php echo $__env->make('components.mobile-hint', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <script src="<?php echo e(asset('assets/scripts/mobile-hint.js?v=' . time())); ?>"></script>

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
    <?php echo $__env->yieldPushContent('scripts'); ?>


</body>

</html>
<?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/layouts/app.blade.php ENDPATH**/ ?>