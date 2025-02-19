@extends('admin.layout')

@section('title', 'Licenses Dashboard')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Licenses Dashboard</h1>
        <p class="mt-2 text-gray-600">Manage your license keys and associated plugins.</p>
    </div>
    <div>
        <a href="{{ route('licenses.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            Create License
        </a>
    </div>
</div>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">ID</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Plugin</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">License Key</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Status</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Purchased At</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($licenses as $license)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 border-b border-gray-200">{{ $license->id }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $license->plugin->name }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $license->license_key }}</td>
                <td class="py-3 px-4 border-b border-gray-200">{{ ucfirst($license->status) }}</td>
                <td class="py-3 px-4 border-b border-gray-200">
                    {{ $license->purchased_at ? $license->purchased_at->format('Y-m-d') : '' }}
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    <a href="{{ route('licenses.edit', $license->id) }}" class="text-blue-500 hover:underline">Edit</a>
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
                <td colspan="6" class="py-3 px-4 text-center text-gray-500">No licenses found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection