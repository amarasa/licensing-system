<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Plugin;
use App\Models\License;
use App\Models\Setting;
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

        // Parse the provided domain (only consider host portion).
        $providedDomain = strtolower(parse_url($data['domain'], PHP_URL_HOST));

        // Retrieve dev extensions from settings.
        $rawDevExtensions = Setting::getValue('dev_extensions', 'localhost,.local,.test,.dev,.loc');
        $decoded = json_decode($rawDevExtensions, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $devExtensions = array_map(function ($item) {
                return strtolower($item['value'] ?? '');
            }, $decoded);
        } else {
            $devExtensions = array_map('trim', explode(',', strtolower($rawDevExtensions)));
        }

        // Determine if the provided domain is a development domain.
        $isDevDomain = false;
        foreach ($devExtensions as $ext) {
            if (strpos($providedDomain, $ext) !== false) {
                $isDevDomain = true;
                break;
            }
        }

        // Count production activations for this license.
        // Production activations: those whose domain host does NOT contain any dev extension.
        $productionActivations = $license->activations->filter(function ($activation) use ($devExtensions) {
            $host = strtolower(parse_url($activation->domain, PHP_URL_HOST));
            foreach ($devExtensions as $ext) {
                if (strpos($host, $ext) !== false) {
                    return false;
                }
            }
            return true;
        });

        // Check if the provided domain is already activated.
        $existingActivation = $license->activations->first(function ($activation) use ($providedDomain) {
            $host = strtolower(parse_url($activation->domain, PHP_URL_HOST));
            return $host === $providedDomain;
        });

        // If the domain is production (i.e. not a dev domain) and is new,
        // enforce the domain limit.
        if (!$isDevDomain && !$existingActivation) {
            if ($license->domain_limit > 0 && $productionActivations->count() >= $license->domain_limit) {
                return response()->json(['error' => 'Activation limit reached for production domains'], 403);
            }
        }

        // Record the activation if not already recorded.
        $activation = $license->activations()->firstOrCreate(
            ['domain' => $data['domain']],
            ['activated_at' => now()]
        );
        $activatedAt = $activation->activated_at instanceof Carbon
            ? $activation->activated_at
            : Carbon::parse($activation->activated_at);

        // Fetch the latest release data from GitHub dynamically using the plugin's GitHub repo.
        $githubRepo = $plugin->github_repo; // e.g., "amarasa/querycraft"
        $githubResponse = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json'
        ])->get("https://api.github.com/repos/{$githubRepo}/releases/latest");

        if (!$githubResponse->successful()) {
            return response()->json(['error' => 'Unable to fetch release information'], 500);
        }
        $releaseData = $githubResponse->json();
        $tag = $releaseData['tag_name'];
        $downloadUrl = "https://github.com/{$githubRepo}/archive/refs/tags/{$tag}.zip";
        $releaseVersion = ltrim($tag, 'v');

        // Build update metadata with required top-level keys.
        $updateData = [
            'name'         => $plugin->name,
            'version'      => $releaseVersion,
            'download_url' => $downloadUrl,
            'sections'     => [
                'changelog'   => $releaseData['body'] ?? 'No changelog provided.',
                'description' => $plugin->description,
            ],
            'author'       => $plugin->author,
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

    public function validateLicense(Request $request)
    {
        // Validate incoming request data.
        $data = $request->validate([
            'license_key' => 'required|string',
            'plugin_slug' => 'required|string',
            'domain'      => 'required|url',
        ]);

        // Retrieve the plugin by its slug.
        $plugin = \App\Models\Plugin::where('slug', $data['plugin_slug'])->first();
        if (!$plugin) {
            return response()->json(['error' => 'Plugin not found'], 404);
        }

        // Retrieve the license associated with the plugin.
        $license = \App\Models\License::where('license_key', $data['license_key'])
            ->where('plugin_id', $plugin->id)
            ->first();
        if (!$license) {
            return response()->json(['error' => 'License key is invalid'], 404);
        }
        if ($license->status !== 'active') {
            return response()->json(['error' => 'License is not active'], 403);
        }

        // Parse the provided domain's host.
        $providedDomain = strtolower(parse_url($data['domain'], PHP_URL_HOST));

        // Retrieve dev extensions from settings.
        $rawDevExtensions = \App\Models\Setting::getValue('dev_extensions', 'localhost,.local,.test,.dev,.loc');
        $decoded = json_decode($rawDevExtensions, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $devExtensions = array_map(function ($item) {
                return strtolower($item['value'] ?? '');
            }, $decoded);
        } else {
            $devExtensions = array_map('trim', explode(',', strtolower($rawDevExtensions)));
        }

        // Determine if the provided domain is a development domain.
        $isDevDomain = false;
        foreach ($devExtensions as $ext) {
            if (strpos($providedDomain, $ext) !== false) {
                $isDevDomain = true;
                break;
            }
        }

        // Refresh activations from the database.
        $activations = $license->activations()->get();

        // Count production activations (exclude those in dev environments).
        $productionActivations = $activations->filter(function ($activation) use ($devExtensions) {
            $host = strtolower(parse_url($activation->domain, PHP_URL_HOST));
            foreach ($devExtensions as $ext) {
                if (strpos($host, $ext) !== false) {
                    return false;
                }
            }
            return true;
        });

        // Check if the provided domain is already activated.
        $existingActivation = $activations->first(function ($activation) use ($providedDomain) {
            $host = strtolower(parse_url($activation->domain, PHP_URL_HOST));
            return $host === $providedDomain;
        });

        // If the provided domain is production and not already activated, enforce the domain limit.
        if (!$isDevDomain && !$existingActivation) {
            if ($license->domain_limit > 0 && $productionActivations->count() >= $license->domain_limit) {
                return response()->json(['error' => 'Activation limit reached for production domains'], 403);
            }
        }

        // Record the activation if not already recorded.
        $activation = $license->activations()->firstOrCreate(
            ['domain' => $data['domain']],
            ['activated_at' => now()]
        );

        return response()->json(['message' => 'License key is valid'], 200);
    }
}
