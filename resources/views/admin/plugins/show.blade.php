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

@forelse ($plugin->licenses as $license)
<div class="mb-8 border border-gray-200 rounded p-4">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">License: {{ $license->license_key }}</h2>
    @if ($license->activations->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg border border-gray-200">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left border-b border-gray-200">Domain</th>
                    <th class="py-3 px-4 text-left border-b border-gray-200">Activated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($license->activations as $activation)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border-b border-gray-200">{{ $activation->domain }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        {{ $activation->activated_at ? $activation->activated_at->format('F jS, Y') : '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-gray-500">No activations found for this license.</p>
    @endif
</div>
@empty
<div class="mb-8">
    <p class="text-gray-500">No licenses found for this plugin.</p>
</div>
@endforelse

@endsection