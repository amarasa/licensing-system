<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite('resources/css/app.css')
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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

    <!-- Include jQuery (required by toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Custom Script for Delete Confirmation and Toastr -->
    <script>
        $(document).ready(function() {
            // Attach SweetAlert2 confirmation for delete forms with class 'delete-plugin'
            $('.delete-plugin, .delete-license, .delete-activation').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Display toastr notifications if there is a session message
            @if(session('success'))
            toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
            toastr.error("{{ session('error') }}");
            @endif
        });
    </script>

    @yield('scripts')

</body>

</html>