<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Toastr -->
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #212529;
        }

        /* Card / container styling */
        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        /* Tabs */
        .auth-tabs button {
            border: 1px solid #dee2e6;
            background: transparent;
            color: #6c757d;
            padding: 8px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .auth-tabs button.active {
            background: #ffffff;
            color: #000;
            border-color: #ced4da;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* Inputs */
        input.form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        input.form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15);
        }

        /* Buttons */
        .btn-primary {
            border-radius: 8px;
            padding: 10px;
        }
    </style>
</head>

<body>

@yield('content')

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

<script>
/* -----------------------
   Toastr (server messages)
----------------------- */
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 4000
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


/* -----------------------
   UI only (no validation)
----------------------- */

// Toggle forms
function showLogin() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('registerForm').style.display = 'none';

    document.getElementById('loginTab').classList.add('active');
    document.getElementById('registerTab').classList.remove('active');
}

function showRegister() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';

    document.getElementById('registerTab').classList.add('active');
    document.getElementById('loginTab').classList.remove('active');
}

// Password visibility
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
}
</script>

</body>
</html>