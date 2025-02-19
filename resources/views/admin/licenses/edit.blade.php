@extends('admin.layout')

@section('title', 'Edit License')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Edit License</h1>
        <p class="mt-2 text-gray-600">Update the license details below.</p>
    </div>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg p-6">
        <form action="{{ route('licenses.update', $license->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Plugin Dropdown with Floating Label -->
            <div class="relative">
                <select id="plugin_id" name="plugin_id" required
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <option value="" disabled></option>
                    @foreach ($plugins as $plugin)
                    <option value="{{ $plugin->id }}" {{ $license->plugin_id == $plugin->id ? 'selected' : '' }}>
                        {{ $plugin->name }}
                    </option>
                    @endforeach
                </select>
                <label for="plugin_id"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                    Plugin
                </label>
            </div>

            <!-- Floating label input for License Key -->
            <div class="relative">
                <input type="text" id="license_key" name="license_key" placeholder=" " value="{{ old('license_key', $license->license_key) }}" required
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                <label for="license_key"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                    License Key
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Update License
                </button>
            </div>
        </form>
    </div>
</div>
@endsection