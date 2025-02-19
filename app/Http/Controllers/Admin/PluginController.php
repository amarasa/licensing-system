<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PluginController extends Controller
{
    public function index()
    {
        $plugins = Plugin::all();
        return view('admin.plugins.index', compact('plugins'));
    }

    public function create()
    {
        return view('admin.plugins.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'required|string|max:255|unique:plugins,slug',
            'current_version' => 'nullable|string|max:50', // Optional: you may eventually remove this field.
            'github_repo'     => 'required|string',
            'author'          => 'required|string|max:255',
            'description'     => 'required|string',
        ]);

        Plugin::create($data);

        return redirect()->route('plugins.index')->with('success', 'Plugin created successfully!');
    }

    /**
     * Show the form for editing an existing plugin.
     */
    public function edit(Plugin $plugin)
    {
        return view('admin.plugins.edit', compact('plugin'));
    }

    /**
     * Update the plugin with new data.
     */
    public function update(Request $request, Plugin $plugin)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'required|string|max:255|unique:plugins,slug,' . $plugin->id,
            'current_version' => 'nullable|string|max:50',
            'github_repo'     => 'required|string',
            'author'          => 'required|string|max:255',
            'description'     => 'required|string',
        ]);

        $plugin->update($data);

        return redirect()->route('plugins.index')->with('success', 'Plugin updated successfully!');
    }

    /**
     * Delete the plugin.
     */
    public function destroy(Plugin $plugin)
    {
        $plugin->delete();
        return redirect()->route('plugins.index')->with('success', 'Plugin deleted successfully!');
    }

    public function show(Plugin $plugin)
    {
        // Eager load licenses and their activations.
        $plugin->load('licenses.activations');

        // Fetch the latest release version from GitHub using the stored repo.
        $githubRepo = $plugin->github_repo;
        $githubResponse = \Illuminate\Support\Facades\Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json'
        ])->get("https://api.github.com/repos/{$githubRepo}/releases/latest");

        if ($githubResponse->successful()) {
            $releaseData = $githubResponse->json();
            $latestVersion = ltrim($releaseData['tag_name'], 'v');
        } else {
            $latestVersion = 'N/A';
        }

        return view('admin.plugins.show', compact('plugin', 'latestVersion'));
    }

    public function releases(Plugin $plugin)
    {
        // Fetch *all* releases for this plugin
        $allReleases = [];
        if ($plugin->github_repo) {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json'
            ])->get("https://api.github.com/repos/{$plugin->github_repo}/releases");
            if ($response->successful()) {
                $allReleases = $response->json();
            }
        }

        return view('admin.plugins.releases', [
            'plugin' => $plugin,
            'releases' => $allReleases
        ]);
    }
}
