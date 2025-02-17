<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plugin;
use App\Models\License;

class LicenseApiController extends Controller
{
    public function verify(Request $request)
    {
        // Validate incoming request data
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

        if (!$license) {
            return response()->json(['error' => 'License not found'], 404);
        }

        if ($license->status !== 'active') {
            return response()->json(['error' => 'License is not active'], 403);
        }

        // Record the activation for this domain if not already recorded
        // This will either retrieve an existing activation or create a new one.
        $activation = $license->activations()->firstOrCreate(
            ['domain' => $data['domain']],
            ['activated_at' => now()]
        );

        // Build update metadata (adjust the download URL as needed)
        $updateData = [
            'plugin' => [
                'name'         => $plugin->name,
                'version'      => $plugin->current_version,
                'download_url' => url("/downloads/{$plugin->slug}.zip"),
            ],
            'license' => [
                'license_key' => $license->license_key,
                'status'      => $license->status,
            ],
            'activation' => [
                'domain'       => $activation->domain,
                'activated_at' => $activation->activated_at,
            ],
        ];

        return response()->json($updateData);
    }
}
