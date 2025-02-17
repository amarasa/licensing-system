@extends('admin.layout')

@section('title', 'Create New Plugin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Create New Plugin</h1>
        <p class="mt-2 text-gray-600">Fill in the details to add a new plugin.</p>
    </div>

    <!-- Display validation errors if any -->
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
        <form action="{{ route('plugins.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Floating label input for Plugin Name -->
            <div class="relative">
                <input type="text" id="name" name="name" placeholder=" " value="{{ old('name') }}" required
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                <label for="name"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:bg-white bg-transparent">
                    Plugin Name
                </label>
            </div>


            <!-- Floating label input for Slug -->
            <div class="relative">
                <input type="text" id="slug" name="slug" placeholder=" " value="{{ old('slug') }}" required
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                <label for="slug"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:bg-white bg-transparent">
                    Slug
                </label>
            </div>

            <!-- Floating label input for Current Version -->
            <div class="relative">
                <input type="text" id="current_version" name="current_version" placeholder=" " value="{{ old('current_version') }}"
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                <label for="current_version"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:bg-white bg-transparent">
                    Current Version
                </label>
            </div>


            <!-- Submit button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create Plugin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection