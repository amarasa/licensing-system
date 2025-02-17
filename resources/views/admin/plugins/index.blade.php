<!-- resources/views/admin/plugins/index.blade.php -->
@extends('admin.layout')

@section('title', 'Plugins Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Plugins Dashboard</h1>
    <p class="mt-2 text-gray-600">Manage your plugins and licensing details.</p>
</div>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">ID</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Name</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Slug</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Version</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($plugins as $plugin)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 border-b border-gray-200">{{ $plugin->id }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $plugin->name }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $plugin->slug }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $plugin->current_version }}</td>
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="{{ route('plugins.edit', $plugin->id) }}" class="text-blue-500 hover:underline">Edit</a>
                    <a href="{{ route('plugins.show', $plugin->id) }}" class="ml-4 text-green-500 hover:underline">View</a>
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