<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - LaraOps</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    <link rel="stylesheet" href="{{ asset('vendor/laravelops/css/app.css') }}">
</head>
<body class="font-sans antialiased">
    <div id="app"></div>

    <script>
        // Set up CSRF token for Axios
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('vendor/laravelops/js/app.js') }}" defer></script>
</body>
</html> 