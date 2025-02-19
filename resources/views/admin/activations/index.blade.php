@extends('admin.layout')

@section('title', 'Activations Dashboard')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Activations Dashboard</h1>
        <p class="mt-2 text-gray-600">List of activations (domains where licenses are active).</p>
    </div>
</div>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg border border-gray-200">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="py-3 px-4 text-left border-b border-gray-200">ID</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">License Key</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Domain</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Activated On</th>
                <th class="py-3 px-4 text-left border-b border-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activations as $activation)
            <tr class="hover:bg-gray-50">
                <td class="py-3 px-4 border-b border-gray-200">{{ $activation->id }}</td>
                <td class="py-3 px-4 border-b border-gray-200">
                    {{ $activation->license->license_key ?? 'N/A' }}
                </td>
                <td class="py-3 px-4 border-b border-gray-200">{{ $activation->domain }}</td>
                <td class="py-3 px-4 border-b border-gray-200">
                    {{ $activation->activated_at ? $activation->activated_at->format('F jS, Y') : '' }}
                </td>
                <td class="py-3 px-4 border-b border-gray-200">
                    <form action="{{ route('activations.destroy', $activation->id) }}" method="POST" class="inline-block ml-2 delete-activation">
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
                <td colspan="5" class="py-3 px-4 text-center text-gray-500">
                    No activation records found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection