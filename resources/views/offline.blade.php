<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Offline | PPI Check</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">
    <style>
        body {
            min-height: 100dvh;
            display: grid;
            place-items: center;
            background: radial-gradient(800px 400px at 20% 0%, rgba(13, 122, 102, .15), transparent 60%), #eef5f3;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .offline-card {
            max-width: 420px;
            text-align: center;
            background: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem 1.75rem;
            box-shadow: 0 20px 50px rgba(7, 61, 51, .12);
            margin: 1rem;
        }
        .offline-icon {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: #e6f4f1;
            color: #0d7a66;
            display: grid;
            place-items: center;
            font-size: 2.3rem;
            margin: 0 auto 1.25rem;
        }
    </style>
</head>
<body>
    <div class="offline-card">
        <div class="offline-icon"><i class="bi bi-wifi-off"></i></div>
        <h1 class="h4 fw-bold mb-2">Tidak Ada Koneksi</h1>
        <p class="text-secondary small mb-4">
            Anda sedang offline. Periksa koneksi internet Anda, lalu coba muat ulang halaman.
            Beberapa halaman yang pernah dibuka tetap dapat diakses dari cache.
        </p>
        <a href="/" class="btn text-white rounded-pill px-4 py-2 fw-semibold" style="background:#0d7a66">
            <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
        </a>
    </div>
</body>
</html>
