@extends('admin.layout')

@section('title', 'Activations Dashboard')

@section('content')
@php
// Group activations by license key (assumes $activations is passed from the controller)
$groupedActivations = $activations->groupBy(function($activation) {
return $activation->license->license_key;
});
@endphp

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-4 md:mb-0">
        <h1 class="text-3xl font-bold text-gray-800">Activations Dashboard</h1>
        <p class="mt-2 text-gray-600">Grouped by License Key</p>
    </div>
    <div>
        <input id="activation-search" type="text" placeholder="Search by license or domain..."
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

<div class="overflow-x-auto">
    <table id="activations-table" class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">License</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Domain</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Activated At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groupedActivations as $licenseKey => $activationGroup)
            @php
            // Get the first activation (to display its domain and date)
            $firstActivation = $activationGroup->first();
            $visibleDomain = $firstActivation->domain;
            $activatedAt = $firstActivation->activated_at
            ? $firstActivation->activated_at->format('F jS, Y')
            : 'N/A';
            $extraCount = $activationGroup->count() - 1;
            // Create a string with all domains (space separated) for search filtering.
            $allDomains = $activationGroup->pluck('domain')->implode(' ');
            @endphp
            <tr class="activation-row hover:bg-gray-50" data-all-domains="{{ $allDomains }}">
                <td class="py-3 px-4 border-b border-gray-200 activation-license">
                    <a href="{{ route('licenses.show', $firstActivation->license->id) }}" class="text-blue-500 hover:underline">
                        {{ $licenseKey }}
                    </a>
                </td>
                <td class="py-3 px-4 border-b border-gray-200 activation-domain">
                    {{ $visibleDomain }} @if($extraCount > 0) (+{{ $extraCount }} more) @endif
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    {{ $activatedAt }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="py-3 px-4 text-center text-gray-500">No activation records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    // Live search: filter rows based on license key or domains.
    // Also, if the search term isn't found in the visible domain but is found in additional domains,
    // append an indicator.
    document.getElementById('activation-search').addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('.activation-row');
        rows.forEach(function(row) {
            let licenseText = row.querySelector('.activation-license').textContent.toLowerCase();
            let domainCell = row.querySelector('.activation-domain');
            // Store original content if not already stored.
            if (!domainCell.dataset.original) {
                domainCell.dataset.original = domainCell.innerHTML;
            }
            let originalDomainHTML = domainCell.dataset.original;
            let visibleDomain = originalDomainHTML.toLowerCase();
            let allDomains = row.getAttribute('data-all-domains').toLowerCase();

            // Determine if the row should be shown.
            if (licenseText.indexOf(searchTerm) > -1 || allDomains.indexOf(searchTerm) > -1) {
                row.style.display = '';
                // If the search term isn't in the visible domain text but is found in the full list,
                // append an indicator.
                if (visibleDomain.indexOf(searchTerm) === -1 && allDomains.indexOf(searchTerm) > -1) {
                    domainCell.innerHTML = originalDomainHTML + ' <span class="text-sm text-blue-500">(matched in additional domain)</span>';
                } else {
                    domainCell.innerHTML = originalDomainHTML;
                }
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection