@extends('admin.layout')

@section('title', 'License Details')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">License Details</h1>
    <p class="mt-2 text-gray-600"><strong>License Key:</strong> {{ $license->license_key }}</p>
    <p class="mt-2 text-gray-600">
        <strong>Activation Limit:</strong>
        {{ $license->domain_limit == 0 ? 'Unlimited' : $license->domain_limit }}
    </p>
</div>

<div class="mb-8 border border-gray-200 rounded p-4">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Associated Plugin</h2>
    @if($license->plugin)
    <p class="mt-1 text-gray-600"><strong>Name:</strong> {{ $license->plugin->name }}</p>
    <p class="mt-1 text-gray-600"><strong>Author:</strong> {{ $license->plugin->author }}</p>
    <p class="mt-1 text-gray-600"><strong>Description:</strong> {{ $license->plugin->description }}</p>
    <p class="mt-1 text-gray-600">
        <strong>GitHub Repository:</strong>
        <a href="https://github.com/{{ $license->plugin->github_repo }}" target="_blank" class="text-blue-500 hover:underline">
            {{ $license->plugin->github_repo }}
        </a>
    </p>
    @else
    <p class="text-gray-500">No associated plugin information found.</p>
    @endif
</div>

@php
// Retrieve the dev extensions setting from the database.
use App\Models\Setting;
$rawDevExtensions = Setting::getValue('dev_extensions', 'localhost,.local,.test,.dev,.loc');

// Decode the JSON. If it fails, fallback to comma-separated.
$decoded = json_decode($rawDevExtensions, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
$devExtensions = array_map(function($item) {
return $item['value'] ?? '';
}, $decoded);
} else {
$devExtensions = array_map('trim', explode(',', $rawDevExtensions));
}

// Group activations into Production and Development.
$productionActivations = collect();
$developmentActivations = collect();
foreach($license->activations as $activation) {
$domain = strtolower($activation->domain);
$isDev = false;
foreach($devExtensions as $ext) {
if (strpos($domain, strtolower($ext)) !== false) {
$isDev = true;
break;
}
}
if ($isDev) {
$developmentActivations->push($activation);
} else {
$productionActivations->push($activation);
}
}
@endphp

<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Production Activations</h2>
    @if($productionActivations->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg border border-gray-200">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left border-b border-gray-200">Domain</th>
                    <th class="py-3 px-4 text-left border-b border-gray-200">Activated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productionActivations as $activation)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border-b border-gray-200">{{ $activation->domain }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        {{ $activation->activated_at ? $activation->activated_at->format('F jS, Y') : 'N/A' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-gray-500">No production activations found for this license.</p>
    @endif
</div>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Development Activations</h2>
    @if($developmentActivations->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg border border-gray-200">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left border-b border-gray-200">Domain</th>
                    <th class="py-3 px-4 text-left border-b border-gray-200">Activated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($developmentActivations as $activation)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border-b border-gray-200">{{ $activation->domain }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        {{ $activation->activated_at ? $activation->activated_at->format('F jS, Y') : 'N/A' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-gray-500">No development activations found for this license.</p>
    @endif
</div>
@endsection