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

<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Licenses</h2>
    @if($plugin->licenses->isEmpty())
    <p class="text-gray-500">No licenses found for this plugin.</p>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg border border-gray-200">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left border-b border-gray-200">License</th>
                    <th class="py-3 px-4 text-left border-b border-gray-200">Domain</th>
                    <th class="py-3 px-4 text-left border-b border-gray-200">Activated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plugin->licenses as $license)
                @php
                $activations = $license->activations;
                $activationCount = $activations->count();
                $firstActivation = $activationCount > 0 ? $activations->first() : null;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border-b border-gray-200">{{ $license->license_key }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        @if($firstActivation)
                        {{ $firstActivation->domain }}
                        @if($activationCount > 1)
                        ( + {{ $activationCount - 1 }} more )
                        @endif
                        @else
                        N/A
                        @endif
                    </td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        @if($firstActivation && $firstActivation->activated_at)
                        {{ $firstActivation->activated_at->format('F jS, Y') }}
                        @else
                        N/A
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection