<!DOCTYPE html>
<html>
<head>
    <title>Roster Check-In</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: Arial;
            background: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            width: 350px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .spinner {
            border: 4px solid #eee;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin: auto;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }

        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>

<body>

<div class="card">
    <div id="loading">
        <div class="spinner"></div>
        <p>Checking in...</p>
    </div>

    <div id="result" style="display:none;"></div>
</div>

<script>
const rosterId = "{{ $roster_id }}";
const checksum  = "{{ $checksum }}";

function checkIn() {
    fetch('/checkin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            roster_id: rosterId,
            checksum: checksum
        })
    })
    .then(res => res.json())
    .then(data => {

        document.getElementById('loading').style.display = 'none';
        const result = document.getElementById('result');
        result.style.display = 'block';

        if (data.success) {
            result.innerHTML = `
                <div class="success">✔ ${data.message}</div>
                <p>Status: ${data.status}</p>
                <p>Event: ${data.event ?? ''}</p>
                <p>Org: ${data.organization ?? ''}</p>
            `;
        } else {
            result.innerHTML = `
                <div class="error">✖ ${data.message}</div>
            `;
        }
    })
    .catch(err => {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('result').style.display = 'block';
        document.getElementById('result').innerHTML =
            `<div class="error">System Error</div>`;
    });
}

checkIn();
</script>

</body>
</html>