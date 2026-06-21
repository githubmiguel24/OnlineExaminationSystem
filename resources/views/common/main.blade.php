<!DOCTYPE html>
<html lang="en">
<head>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
</head>
<body class="d-flex flex-column min-vh-100">
    
    @include ('common.header')
    
    @yield('content')
    
    @include ('common.footer')

</body>
</html>