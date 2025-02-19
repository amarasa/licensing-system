@extends('admin.layout')

@section('title', 'Plugins Dashboard')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
    <div class="mb-4 md:mb-0">
        <h1 class="text-3xl font-bold text-gray-800">Plugins Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage your plugins and licensing details.</p>
    </div>
    <div class="flex items-center space-x-4">
        <!-- Live Search Input -->
        <input id="plugin-search" type="text" placeholder="Search plugins..."
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <!-- Add Plugin Button -->
        <a href="{{ route('plugins.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            Add Plugin
        </a>
    </div>
</div>

<div class="overflow-x-auto">
    <table id="plugins-table" class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">Name</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Slug</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Version</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">GitHub Repo</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($plugins as $plugin)
            @php
            // Fetch GitHub release data for this plugin.
            // In production, cache this to avoid performance issues.
            $githubRepo = $plugin->github_repo;
            $githubResponse = \Illuminate\Support\Facades\Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json'
            ])->get("https://api.github.com/repos/{$githubRepo}/releases/latest");

            if ($githubResponse->successful()) {
            $releaseData = $githubResponse->json();
            $tag = $releaseData['tag_name'];
            $version = ltrim($tag, 'v'); // Remove leading "v"
            $downloadUrl = "https://github.com/{$githubRepo}/archive/refs/tags/{$tag}.zip";
            } else {
            $version = 'N/A';
            $downloadUrl = '#';
            }
            @endphp
            <tr class="plugin-row hover:bg-gray-50">
                <td class="py-3 px-4 border-b border-gray-200 plugin-name">{{ $plugin->name }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $plugin->slug }}</td>
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="{{ $downloadUrl }}" target="_blank" class="text-blue-500 hover:underline">{{ $version }}</a>
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="https://github.com/{{ $plugin->github_repo }}" target="_blank" class="text-blue-500 hover:underline">
                        {{ $plugin->github_repo }}
                    </a>
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="{{ route('plugins.show', $plugin->id) }}" class="text-green-500 hover:underline">View</a>
                    <form action="{{ route('plugins.destroy', $plugin->id) }}" method="POST" class="inline-block ml-2 delete-plugin">
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
                <td colspan="5" class="py-3 px-4 text-center text-gray-500">No plugins found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('plugin-search').addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('.plugin-row');
        rows.forEach(function(row) {
            let pluginName = row.querySelector('.plugin-name').textContent.toLowerCase();
            row.style.display = pluginName.indexOf(searchTerm) > -1 ? '' : 'none';
        });
    });
</script>
@endsection