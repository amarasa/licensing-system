@extends('admin.layout')

@section('title', 'Licenses Dashboard')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-4 md:mb-0">
        <h1 class="text-3xl font-bold text-gray-800">Licenses Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage your licenses.</p>
    </div>
    <div class="flex items-center space-x-4">
        <!-- Live Search Input -->
        <input id="license-search" type="text" placeholder="Search by plugin or license key..."
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <!-- Add License Button -->
        <a href="{{ route('licenses.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            Add License
        </a>
    </div>
</div>

<div class="overflow-x-auto">
    <table id="licenses-table" class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">License</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Plugin</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Activated On</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($licenses as $license)
            @php
            // Get the first activation record for this license (if any)
            $activation = $license->activations->first();
            @endphp
            <tr class="license-row hover:bg-gray-50">
                <!-- License key as a clickable link -->
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="{{ route('licenses.show', $license->id) }}" class="text-blue-500 hover:underline">
                        {{ $license->license_key }}
                    </a>
                </td>
                <!-- Plugin name -->
                <td class="py-3 px-4 border-b border-gray-200 plugin-name">
                    {{ $license->plugin->name ?? 'N/A' }}
                </td>
                <!-- Activated On: use the activated_at from the first activation if available -->
                <td class="py-3 px-4 border-b border-gray-200">
                    {{ $activation && $activation->activated_at ? $activation->activated_at->format('F jS, Y') : 'N/A' }}
                </td>
                <!-- Actions: Only the Delete button -->
                <td class="py-3 px-4 border-b border-gray-200">
                    <form action="{{ route('licenses.destroy', $license->id) }}" method="POST" class="inline-block ml-2 delete-license">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-3 px-4 text-center text-gray-500">No licenses found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    // Live search: filter rows based on plugin name or license key
    document.getElementById('license-search').addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('.license-row');
        rows.forEach(function(row) {
            // Get text from the first cell (license key) and the plugin name cell
            let licenseKey = row.querySelector('td:first-child a').textContent.toLowerCase();
            let pluginName = row.querySelector('.plugin-name').textContent.toLowerCase();
            if (licenseKey.indexOf(searchTerm) > -1 || pluginName.indexOf(searchTerm) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection