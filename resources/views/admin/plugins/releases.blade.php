@extends('admin.layout')

@section('title', 'All Versions - ' . $plugin->name)

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">{{ $plugin->name }} - All Versions</h1>
    <p class="mt-2 text-gray-600">Below is a list of all releases fetched from GitHub.</p>
</div>

@if(empty($releases))
<p class="text-gray-500">No releases found or unable to fetch release data.</p>
@else
<div class="space-y-6">
    @foreach($releases as $release)
    @php
    $tag = $release['tag_name'] ?? '';
    $version = ltrim($tag, 'v');
    $name = $release['name'] ?? '';
    $body = $release['body'] ?? '';
    $publishedAt = isset($release['published_at']) ? date('F jS, Y', strtotime($release['published_at'])) : 'N/A';
    $downloadZip = "https://github.com/{$plugin->github_repo}/archive/refs/tags/{$tag}.zip";
    $downloadTar = "https://github.com/{$plugin->github_repo}/archive/refs/tags/{$tag}.tar.gz";
    @endphp
    <div class="border border-gray-200 rounded p-4">
        <h2 class="text-xl font-bold text-gray-800">
            {{ $name ? $name : $version }}
            <span class="text-sm text-gray-500">({{ $version }})</span>
        </h2>
        <p class="text-sm text-gray-500 mb-2">Published on {{ $publishedAt }}</p>
        <p class="text-gray-700 whitespace-pre-line">{{ $body }}</p>
        <div class="mt-4">
            <h3 class="font-semibold">Assets:</h3>
            <ul class="list-disc list-inside text-blue-500">
                <li>
                    <a href="{{ $downloadZip }}" target="_blank" class="hover:underline">
                        Source code (zip)
                    </a>
                </li>
                <li>
                    <a href="{{ $downloadTar }}" target="_blank" class="hover:underline">
                        Source code (tar.gz)
                    </a>
                </li>
            </ul>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection