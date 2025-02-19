@extends('admin.layout')

@section('title', 'Plugin Details')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">{{ $plugin->name }} Details</h1>
    <p class="mt-2 text-gray-600"><strong>Author:</strong> {{ $plugin->author }}</p>
    <p class="mt-1 text-gray-600"><strong>Description:</strong> {{ $plugin->description }}</p>
    <p class="mt-1 text-gray-600">
        <strong>GitHub Repository:</strong>
        <a href="https://github.com/{{ $plugin->github_repo }}" target="_blank" class="text-blue-500 hover:underline">
            {{ $plugin->github_repo }}
        </a>
    </p>
    <p class="mt-1 text-gray-600"><strong>Latest Version:</strong> {{ $latestVersion }}</p>
</div>

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-4 md:mb-0">
        <h2 class="text-2xl font-bold text-gray-800">Licenses</h2>
    </div>
    <div>
        <input id="license-filter" type="text" placeholder="Search licenses by key or domain..."
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

<div class="overflow-x-auto">
    <table id="licenses-table" class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">License</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Domain</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Activated At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plugin->licenses as $license)
            @php
            // Collect all activation domains for this license.
            $domains = $license->activations->pluck('domain');
            $allDomains = $domains->implode(' ');
            $firstActivation = $license->activations->first();
            $visibleDomain = $firstActivation ? $firstActivation->domain : 'N/A';
            $activatedAt = $firstActivation && $firstActivation->activated_at
            ? $firstActivation->activated_at->format('F jS, Y')
            : 'N/A';
            $extraCount = $license->activations->count() - 1;
            @endphp
            <tr class="license-row hover:bg-gray-50" data-all-domains="{{ $allDomains }}">
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="{{ route('licenses.show', $license->id) }}" class="text-blue-500 hover:underline">
                        {{ $license->license_key }}
                    </a>
                </td>
                <td class="py-3 px-4 border-b border-gray-200 license-domain">
                    {{ $visibleDomain }} @if($extraCount > 0) (+{{ $extraCount }} more) @endif
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    {{ $activatedAt }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="py-3 px-4 text-center text-gray-500">No licenses found for this plugin.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    // Live search for licenses in the plugin show page.
    document.getElementById('license-filter').addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('.license-row');
        rows.forEach(function(row) {
            let licenseKey = row.querySelector('td a').textContent.toLowerCase();
            let domainCell = row.querySelector('.license-domain');

            // Store the original cell content if not already stored.
            if (!domainCell.dataset.original) {
                domainCell.dataset.original = domainCell.innerHTML;
            }
            let originalContent = domainCell.dataset.original;
            let visibleDomain = originalContent.toLowerCase();
            // The data-all-domains attribute contains all activation domains (space-separated).
            let allDomains = row.getAttribute('data-all-domains').toLowerCase();

            // Determine whether to show this row.
            if (licenseKey.indexOf(searchTerm) > -1 || allDomains.indexOf(searchTerm) > -1) {
                row.style.display = '';
                // If search term is found in extra domains but not in the visible domain text,
                // append an indicator.
                if (visibleDomain.indexOf(searchTerm) === -1 && allDomains.indexOf(searchTerm) > -1) {
                    domainCell.innerHTML = originalContent + ' <span class="text-sm text-blue-500">(matched in additional domain)</span>';
                } else {
                    domainCell.innerHTML = originalContent;
                }
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection