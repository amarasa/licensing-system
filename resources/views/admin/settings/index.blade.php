@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Admin Settings</h1>
    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label for="dev_extensions" class="block text-sm font-medium text-gray-700">
                Development Environment Extensions
            </label>
            <input type="text" id="dev_extensions" name="dev_extensions" value="{{ old('dev_extensions', $devExtensions) }}"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
            <p class="mt-1 text-sm text-gray-500">
                Use tags to represent each domain fragment. For example: <code>localhost</code>, <code>.local</code>, <code>.test</code>
            </p>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Initialize Tagify on the dev_extensions input
    var input = document.querySelector('input[name=dev_extensions]');
    new Tagify(input);
</script>
@endsection