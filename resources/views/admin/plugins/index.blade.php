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

<!-- Change this container to allow vertical overflow -->
<div class="relative">
    <table id="plugins-table" class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">Name</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Slug</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Latest Version</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">GitHub Repo</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($plugins as $plugin)
            @php
            // Fetch all releases for this plugin from GitHub.
            $githubRepo = $plugin->github_repo;
            $allResponse = \Illuminate\Support\Facades\Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json'
            ])->get("https://api.github.com/repos/{$githubRepo}/releases");

            if ($allResponse->successful()) {
            $allReleases = $allResponse->json();
            if (!empty($allReleases)) {
            // The first release is the latest
            $tag = $allReleases[0]['tag_name'];
            $latestVersion = ltrim($tag, 'v');
            $downloadUrl = "https://github.com/{$githubRepo}/archive/refs/tags/{$tag}.zip";
            // Grab up to 4 previous releases (if available)
            $previousReleases = array_slice($allReleases, 1, 4);
            } else {
            $latestVersion = 'N/A';
            $downloadUrl = '#';
            $previousReleases = [];
            }
            } else {
            $latestVersion = 'N/A';
            $downloadUrl = '#';
            $previousReleases = [];
            }
            @endphp
            <tr class="plugin-row hover:bg-gray-50">
                <td class="py-3 px-4 border-b border-gray-200 plugin-name">{{ $plugin->name }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $plugin->slug }}</td>
                <td class="py-3 px-4 border-b border-gray-200 relative">
                    <div class="relative">
                        <span class="mr-2">{{ $latestVersion }}</span>
                        @if(!empty($previousReleases))
                        <!-- Dropdown Toggle Button -->
                        <button type="button" class="inline-block text-blue-500 hover:text-blue-700" onclick="toggleDropdown(event)">
                            ▼
                        </button>
                        <!-- Dropdown container with adjusted z-index to ensure visibility -->
                        <div class="version-dropdown absolute bg-white border border-gray-200 rounded shadow-lg p-2 mt-1 hidden" style="z-index:9999;">
                            @foreach($previousReleases as $release)
                            @php
                            $prevTag = $release['tag_name'] ?? '';
                            $prevVersion = ltrim($prevTag, 'v');
                            $prevDownload = "https://github.com/{$githubRepo}/archive/refs/tags/{$prevTag}.zip";
                            @endphp
                            <div class="py-1">
                                <a href="{{ $prevDownload }}" target="_blank" class="text-blue-500 hover:underline">
                                    {{ $prevVersion }}
                                </a>
                            </div>
                            @endforeach
                            <div class="border-t border-gray-300 mt-2 pt-2 text-center">
                                <a href="{{ route('plugins.releases', $plugin->id) }}" class="text-blue-500 hover:underline text-sm">
                                    See All Versions
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    @if($plugin->github_repo)
                    <a href="https://github.com/{{ $plugin->github_repo }}" target="_blank" class="text-blue-500 hover:underline">
                        {{ $plugin->github_repo }}
                    </a>
                    @else
                    N/A
                    @endif
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="{{ route('plugins.show', $plugin->id) }}" class="text-green-500 hover:underline">View</a>
                    <form action="{{ route('plugins.destroy', $plugin->id) }}" method="POST" class="inline-block ml-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Delete</button>
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
    // Live search: filter rows based on plugin name
    document.getElementById('plugin-search').addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('.plugin-row');
        rows.forEach(function(row) {
            let pluginName = row.querySelector('.plugin-name').textContent.toLowerCase();
            row.style.display = pluginName.indexOf(searchTerm) > -1 ? '' : 'none';
        });
    });

    // Toggle dropdown for previous releases
    function toggleDropdown(event) {
        event.stopPropagation();
        let dropdown = event.target.nextElementSibling;
        if (!dropdown) return;
        dropdown.classList.toggle('hidden');
        // Click outside to close the dropdown
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== event.target) {
                dropdown.classList.add('hidden');
            }
        }, {
            once: true
        });
    }
</script>
@endsection