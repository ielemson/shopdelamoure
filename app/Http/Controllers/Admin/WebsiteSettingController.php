<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    public function edit()
    {
        $setting = WebsiteSetting::firstOrCreate([]);

        return view('admin.settings.website', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = WebsiteSetting::firstOrCreate([]);

        $data = $request->validate([
        'website_name' => 'nullable|string|max:255',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
        'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg,webp|max:1024',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string',
        'meta_keywords' => 'nullable|string',
        'phone' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'facebook' => 'nullable|url',
        'instagram' => 'nullable|url',
        'twitter' => 'nullable|url',
        'linkedin' => 'nullable|url',
        'youtube' => 'nullable|url',
        'tiktok' => 'nullable|url',

        'support_name' => 'nullable|string|max:255',
        'support_role' => 'nullable|string|max:255',
        'support_phone' => 'nullable|string|max:50',
        'support_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
        'support_message_title' => 'nullable|string|max:255',
        'support_message_body' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }

            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        $setting->update($data);

        return back()->with('success', 'Website information updated successfully.');
    }
}