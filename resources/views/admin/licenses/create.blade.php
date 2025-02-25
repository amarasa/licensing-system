@extends('admin.layout')

@section('title', 'Create New License')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Create New License</h1>
        <p class="mt-2 text-gray-600">
            Fill in the details to add a new license. Note: Enter the number of production domains allowed.
            Setting it to 0 means Unlimited.
        </p>
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
        <form action="{{ route('licenses.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Dropdown for Plugin selection -->
            <div class="relative">
                <select id="plugin_id" name="plugin_id" required
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <option value="" disabled selected></option>
                    @foreach ($plugins as $plugin)
                    <option value="{{ $plugin->id }}">{{ $plugin->name }}</option>
                    @endforeach
                </select>
                <label for="plugin_id"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                    Plugin
                </label>
            </div>

            <!-- Floating label input for License Key -->
            <div class="relative">
                <input type="text" id="license_key" name="license_key" placeholder=" " value="{{ old('license_key') }}" required
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                <label for="license_key"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                    License Key
                </label>
                <!-- Generate New Key Button -->
                <button type="button" id="generate-key" class="mt-2 inline-block px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    Generate New Key
                </button>
            </div>

            <!-- New Note Field -->
            <div class="relative">
                <textarea id="note" name="note" placeholder=" " rows="4"
                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-600 peer">{{ old('note') }}</textarea>
                <label for="note"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                    Note (Optional)
                </label>
            </div>

            <!-- Domain Limit Input with Unlimited Checkbox -->
            <div>
                <label for="domain_limit" class="block text-sm font-medium text-gray-700">
                    Allowed Production Domains
                </label>
                <div class="mt-1 flex items-center space-x-4">
                    <input type="number" id="domain_limit" name="domain_limit" value="{{ old('domain_limit', 1) }}" min="0"
                        class="block w-32 border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="unlimited-domains" class="form-checkbox" onclick="toggleDomainLimit()">
                        <span class="ml-2 text-sm text-gray-600">Unlimited</span>
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Enter the number of production domains allowed. Check "Unlimited" to allow any number (development domains won’t count).
                </p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create License
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleDomainLimit() {
        var checkbox = document.getElementById('unlimited-domains');
        var domainInput = document.getElementById('domain_limit');
        if (checkbox.checked) {
            domainInput.readOnly = true; // Make input read-only so value is preserved
            domainInput.value = 0; // Set value to 0 for unlimited
        } else {
            domainInput.readOnly = false; // Allow editing
            if (domainInput.value == 0) {
                domainInput.value = 1; // Reset to a default non-zero value
            }
        }
    }

    // Generate a new UUID key for the license.
    document.getElementById('generate-key').addEventListener('click', function() {
        var licenseInput = document.getElementById('license_key');
        // Use crypto.randomUUID() if available.
        if (window.crypto && crypto.randomUUID) {
            licenseInput.value = crypto.randomUUID();
        } else {
            // Fallback: generate a UUID using a simple function.
            licenseInput.value = generateUUID();
        }
    });

    // Fallback UUID generation function (v4 UUID)
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
</script>
@endsection