<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Public Page' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Laravel asset pipeline -->
</head>
<body>
    {{ $slot }}
</body>
</html>
