<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $devExtensions = Setting::getValue('dev_extensions', 'localhost,.local,.test,.dev,.loc');
        return view('admin.settings.index', compact('devExtensions'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'dev_extensions' => 'required|string',
        ]);

        Setting::setValue('dev_extensions', $data['dev_extensions']);
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
