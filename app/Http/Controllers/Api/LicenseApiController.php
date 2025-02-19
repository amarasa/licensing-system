<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Plugin;
use App\Models\License;
use Carbon\Carbon;

class LicenseApiController extends Controller
{
    public function verify(Request $request)
    {
        // Validate incoming request data.
        $data = $request->validate([
            'license_key' => 'required|string',
            'plugin_slug' => 'required|string',
            'domain'      => 'required|url',
        ]);

        // Retrieve the plugin by its slug.
        $plugin = Plugin::where('slug', $data['plugin_slug'])->first();
        if (!$plugin) {
            return response()->json(['error' => 'Plugin not found'], 404);
        }

        // Retrieve the license associated with the plugin.
        $license = License::where('license_key', $data['license_key'])
            ->where('plugin_id', $plugin->id)
            ->first();

        if (!$license) {
            return response()->json(['error' => 'License not found'], 404);
        }

        if ($license->status !== 'active') {
            return response()->json(['error' => 'License is not active'], 403);
        }

        // Record the activation for this domain if not already recorded.
        $activation = $license->activations()->firstOrCreate(
            ['domain' => $data['domain']],
            ['activated_at' => now()]
        );

        // Ensure 'activated_at' is a Carbon instance before formatting.
        $activatedAt = $activation->activated_at instanceof Carbon
            ? $activation->activated_at
            : Carbon::parse($activation->activated_at);

        // Fetch the latest release data from GitHub using the stored GitHub repository.
        $githubRepo = $plugin->github_repo; // e.g., "amarasa/querycraft"
        $githubResponse = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json'
        ])->get("https://api.github.com/repos/{$githubRepo}/releases/latest");

        if (!$githubResponse->successful()) {
            return response()->json(['error' => 'Unable to fetch release information'], 500);
        }

        $releaseData = $githubResponse->json();

        // Build a download URL using the standard GitHub release URL format.
        $tag = $releaseData['tag_name'];
        $downloadUrl = "https://github.com/{$githubRepo}/archive/refs/tags/{$tag}.zip";
        $releaseVersion = ltrim($tag, 'v'); // Remove leading 'v' for comparison if needed

        // Build update metadata with required top-level keys.
        $updateData = [
            'name'         => $plugin->name,
            'version'      => $releaseVersion,
            'download_url' => $downloadUrl,
            'sections'     => [
                'changelog'   => $releaseData['body'] ?? 'No changelog provided.',
                'description' => $plugin->description, // Use the stored description
            ],
            'author'       => $plugin->author, // Use the stored author
            'license_info' => [
                'license_key' => $license->license_key,
                'status'      => $license->status,
            ],
            'activation_info' => [
                'domain'       => $activation->domain,
                'activated_at' => $activatedAt->toIso8601String(),
            ],
        ];

        return response()->json($updateData);
    }
}
