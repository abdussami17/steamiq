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
            background: linear-gradient(135deg,#1a1a1a,#2d2d2d,#1a1a1a);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
     // Update button styles
     document.getElementById('loginTab').style.backgroundColor = '#ffffff';
            document.getElementById('loginTab').style.color = '#000000';
            document.getElementById('registerTab').style.backgroundColor = 'transparent';
            document.getElementById('registerTab').style.color = '#b0b0b0';
}

function showRegister() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';
     // Update button styles
     document.getElementById('registerTab').style.backgroundColor = '#ffffff';
            document.getElementById('registerTab').style.color = '#000000';
            document.getElementById('loginTab').style.backgroundColor = 'transparent';
            document.getElementById('loginTab').style.color = '#b0b0b0';
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
