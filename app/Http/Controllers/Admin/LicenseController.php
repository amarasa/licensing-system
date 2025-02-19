<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
use App\Models\Plugin;
use Illuminate\Support\Facades\Http;


class LicenseController extends Controller
{
    // List all licenses
    public function index()
    {
        $licenses = License::with('plugin')->get();
        return view('admin.licenses.index', compact('licenses'));
    }

    // Show form to create a new license
    public function create()
    {
        // Get all plugins for a dropdown selection.
        $plugins = Plugin::all();
        return view('admin.licenses.create', compact('plugins'));
    }

    // Store a new license
    public function store(Request $request)
    {
        // Validate input data; no status field is expected from the form
        $data = $request->validate([
            'plugin_id'   => 'required|exists:plugins,id',
            'license_key' => 'required|string|unique:licenses,license_key',
        ]);

        // Automatically set new licenses as active
        $data['status'] = 'active';
        $data['purchased_at'] = now();

        License::create($data);

        return redirect()->route('licenses.index')->with('success', 'License created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // Show the form to edit an existing license
    public function edit(License $license)
    {
        // Retrieve plugins for the dropdown (in case you want to change the association)
        $plugins = Plugin::all();
        return view('admin.licenses.edit', compact('license', 'plugins'));
    }

    // Update the license in the database
    public function update(Request $request, License $license)
    {
        $data = $request->validate([
            'plugin_id'   => 'required|exists:plugins,id',
            'license_key' => 'required|string|unique:licenses,license_key,' . $license->id,
        ]);

        $license->update($data);

        return redirect()->route('licenses.index')->with('success', 'License updated successfully!');
    }

    // Delete the license
    public function destroy(License $license)
    {
        $license->delete();
        return redirect()->route('licenses.index')->with('success', 'License deleted successfully!');
    }

    public function verify(Request $request)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'license_key' => 'required|string',
            'plugin_slug' => 'required|string',
            'domain'      => 'required|url',
        ]);

        // Retrieve the plugin by its slug
        $plugin = Plugin::where('slug', $data['plugin_slug'])->first();
        if (!$plugin) {
            return response()->json(['error' => 'Plugin not found'], 404);
        }

        // Retrieve the license associated with the plugin
        $license = License::where('license_key', $data['license_key'])
            ->where('plugin_id', $plugin->id)
            ->first();

        if (!$license || $license->status !== 'active') {
            return response()->json(['error' => 'License invalid or inactive'], 403);
        }

        // (Optional) Record the activation if needed
        $activation = $license->activations()->firstOrCreate(
            ['domain' => $data['domain']],
            ['activated_at' => now()]
        );

        // *** Fetch the latest release from GitHub ***
        // You can later enhance this by storing the repo on the plugin model.
        $githubRepo = 'amarasa/querycraft';
        $githubResponse = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json'
        ])->get("https://api.github.com/repos/{$githubRepo}/releases/latest");

        if (!$githubResponse->successful()) {
            return response()->json(['error' => 'Unable to fetch release information'], 500);
        }

        $releaseData = $githubResponse->json();

        // Build update metadata
        $updateData = [
            'plugin' => [
                'name'         => $plugin->name,
                'version'      => $releaseData['tag_name'], // Latest version tag from GitHub
                'download_url' => $releaseData['zipball_url'], // URL to download the release
            ],
            'license' => [
                'license_key' => $license->license_key,
                'status'      => $license->status,
            ],
            'activation' => [
                'domain'       => $activation->domain,
                'activated_at' => $activation->activated_at->toIso8601String(),
            ],
        ];

        return response()->json($updateData);
    }
}
