<!-- resources/views/admin/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <!-- Tailwind CSS is already built via Vite -->
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <nav class="mb-4">
            <a href="{{ url('/admin/plugins') }}" class="text-blue-500 hover:underline">Plugins</a>
            <a href="{{ url('/admin/licenses') }}" class="ml-4 text-blue-500 hover:underline">Licenses</a>
            <a href="{{ url('/admin/activations') }}" class="ml-4 text-blue-500 hover:underline">Activations</a>
        </nav>
        <div class="bg-white shadow rounded p-4">
            @yield('content')
        </div>
    </div>
</body>

</html>