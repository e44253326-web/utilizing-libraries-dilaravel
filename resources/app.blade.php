<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Toko Dian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        @include('layouts.navigation')
        <main>
            {{ $slot }}
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                window.Echo.private('user.{{ auth()->id() }}')
                    .listen('.OrderPlaced', (e) => {
                        console.log(e);
                        toastr.success('Order #' + e.order_id + ' telah ditempatkan!', 'Notifikasi');
                    });

                @if(auth()->user()->role === 'admin')
                window.Echo.private('admin.transactions')
                    .listen('.OrderPlaced', (e) => {
                        toastr.info('Order baru #' + e.order_id + ' dari ' + e.user_name, 'Admin');
                    });
                @endif
            }
        });
    </script>
    @endauth
</body>
</html>
